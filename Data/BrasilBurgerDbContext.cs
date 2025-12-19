using Brasil_Burger_Client.Models;
using Microsoft.EntityFrameworkCore;

namespace Brasil_Burger_Client.Data;

public class BrasilBurgerDbContext : DbContext
{
    public BrasilBurgerDbContext(DbContextOptions<BrasilBurgerDbContext> options) : base(options)
    {
    }

    public DbSet<Burger> Burgers => Set<Burger>();
    public DbSet<Complement> Complements => Set<Complement>();
    public DbSet<Menu> Menus => Set<Menu>();
    public DbSet<Client> Clients => Set<Client>();
    public DbSet<Zone> Zones => Set<Zone>();
    public DbSet<Livreur> Livreurs => Set<Livreur>();
    public DbSet<Commande> Commandes => Set<Commande>();
    public DbSet<LigneCommande> LignesCommande => Set<LigneCommande>();
    public DbSet<Paiement> Paiements => Set<Paiement>();

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        base.OnModelCreating(modelBuilder);

        modelBuilder.Entity<Burger>().ToTable("burgers");
        modelBuilder.Entity<Complement>().ToTable("complements");
        modelBuilder.Entity<Menu>().ToTable("menus");
        modelBuilder.Entity<Client>().ToTable("clients");
        modelBuilder.Entity<Zone>().ToTable("zones");
        modelBuilder.Entity<Livreur>().ToTable("livreurs");
        modelBuilder.Entity<Commande>().ToTable("commandes");
        modelBuilder.Entity<LigneCommande>().ToTable("lignes_commande");
        modelBuilder.Entity<Paiement>().ToTable("paiements");

        modelBuilder.Entity<Complement>()
            .HasMany(c => c.MenusBoisson)
            .WithOne(m => m.Boisson)
            .HasForeignKey(m => m.BoissonId)
            .OnDelete(DeleteBehavior.Restrict);

        modelBuilder.Entity<Complement>()
            .HasMany(c => c.MenusFrite)
            .WithOne(m => m.Frite)
            .HasForeignKey(m => m.FriteId)
            .OnDelete(DeleteBehavior.Restrict);

        modelBuilder.Entity<Menu>()
            .HasOne(m => m.Burger)
            .WithMany()
            .HasForeignKey(m => m.BurgerId)
            .OnDelete(DeleteBehavior.Restrict);

        modelBuilder.Entity<Commande>()
            .HasIndex(c => c.ClientId);

        modelBuilder.Entity<Commande>()
            .HasIndex(c => c.DateCommande);

        modelBuilder.Entity<Commande>()
            .HasIndex(c => c.Etat);

        modelBuilder.Entity<Paiement>()
            .HasOne(p => p.Commande)
            .WithOne(c => c.Paiement)
            .HasForeignKey<Paiement>(p => p.CommandeId);
    }
}

