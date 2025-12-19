using Brasil_Burger_Client.Data;
using Brasil_Burger_Client.Services;
using Microsoft.EntityFrameworkCore;
using Npgsql;

var builder = WebApplication.CreateBuilder(args);

// Add services to the container.
builder.Services.AddControllersWithViews();

// Try to use Postgres from configuration, fallback to local SQLite if Postgres unreachable
var defaultConn = builder.Configuration.GetConnectionString("DefaultConnection");
var usedProvider = "postgres";
if (!string.IsNullOrEmpty(defaultConn))
{
        try
        {
                // quick connectivity check with short timeout
                var csb = new NpgsqlConnectionStringBuilder(defaultConn)
                {
                        Timeout = 5
                };
                using (var test = new NpgsqlConnection(csb.ConnectionString))
                {
                        test.Open();
                        test.Close();
                }
                builder.Services.AddDbContext<BrasilBurgerDbContext>(options =>
                        options.UseNpgsql(defaultConn));
                usedProvider = "postgres";
        }
        catch
        {
                // fallback to Sqlite for local development
                builder.Services.AddDbContext<BrasilBurgerDbContext>(options =>
                        options.UseSqlite("Data Source=local_dev.db"));
                usedProvider = "sqlite";
        }
}
else
{
        builder.Services.AddDbContext<BrasilBurgerDbContext>(options =>
                options.UseSqlite("Data Source=local_dev.db"));
        usedProvider = "sqlite";
}

builder.Services.AddDistributedMemoryCache();
builder.Services.AddSession(options =>
{
    options.Cookie.HttpOnly = true;
    options.Cookie.IsEssential = true;
});
builder.Services.AddHttpContextAccessor();
builder.Services.AddScoped<CartService>();

var app = builder.Build();

// Global middleware to catch DB connectivity timeouts and return a friendly message
app.Use(async (context, next) =>
{
        try
        {
                await next();
        }
        catch (Exception ex)
        {
                // inspect inner exceptions for Npgsql / timeout indicators
                Exception? e = ex;
                var isDbConn = false;
                while (e != null)
                {
                        if (e.GetType().Name.Contains("Npgsql") || (e.Message != null && (e.Message.Contains("Failed to connect") || e.Message.Contains("Timeout during connection attempt"))))
                        {
                                isDbConn = true;
                                break;
                        }
                        e = e.InnerException;
                }

                if (isDbConn)
                {
                        context.Response.StatusCode = 503;
                        context.Response.ContentType = "text/html; charset=utf-8";
                        await context.Response.WriteAsync("<h2>Base de données inaccessible</h2><p>Impossible de contacter le serveur de base de données pour le moment. Réessayez dans quelques instants.</p>");
                        return;
                }

                throw;
        }
});

// Configure the HTTP request pipeline.
if (!app.Environment.IsDevelopment())
{
        app.UseExceptionHandler("/Home/Error");
        // The default HSTS value is 30 days. You may want to change this for production scenarios, see https://aka.ms/aspnetcore-hsts.
        app.UseHsts();
}

app.UseHttpsRedirection();
app.UseStaticFiles();

app.UseRouting();
app.UseSession();

app.UseAuthorization();

// Sync PostgreSQL identity sequences with existing max(id) values to avoid duplicate key errors
using (var scope = app.Services.CreateScope())
{
        try
        {
                var db = scope.ServiceProvider.GetRequiredService<BrasilBurgerDbContext>();
                var conn = db.Database.GetDbConnection();
                // only attempt sequence sync when using Postgres provider
                if (conn.GetType().Name.Contains("Npgsql"))
                {
                    conn.Open();

                // List of tables with identity primary keys to sync
                var tables = new[]
                {
                        "burgers",
                        "complements",
                        "menus",
                        "clients",
                        "zones",
                        "livreurs",
                        "commandes",
                        "lignes_commande",
                        "paiements"
                };

                foreach (var table in tables)
                {
                        try
                        {
                                using var cmd = conn.CreateCommand();
                                // get sequence name for identity/serial
                                cmd.CommandText = $"SELECT pg_get_serial_sequence('{table}', 'id')";
                                var seqObj = cmd.ExecuteScalar();
                                if (seqObj == null || seqObj == DBNull.Value) continue;
                                var seqName = seqObj.ToString();

                                // get max id
                                cmd.CommandText = $"SELECT COALESCE(MAX(id),0) FROM {table}";
                                var maxObj = cmd.ExecuteScalar();
                                var maxId = maxObj == null || maxObj == DBNull.Value ? 0L : Convert.ToInt64(maxObj);

                                // set sequence to max(id)+1 (next nextval should return that value)
                                cmd.CommandText = $"SELECT setval('{seqName}', {maxId + 1}, false)";
                                cmd.ExecuteScalar();
                        }
                        catch { /* ignore per-table errors */ }
                }

                    conn.Close();
                }
        }
        catch
        {
                // ignore startup sync failures
        }
}

app.MapControllerRoute(
    name: "default",
    pattern: "{controller=Catalog}/{action=Index}/{id?}");

app.Run();
