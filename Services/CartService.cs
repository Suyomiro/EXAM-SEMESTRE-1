using System.Text.Json;
using Brasil_Burger_Client.Data;
using Brasil_Burger_Client.Models.ViewModels;
using Microsoft.EntityFrameworkCore;

namespace Brasil_Burger_Client.Services;

public class CartService
{
    private readonly BrasilBurgerDbContext _db;
    private readonly IHttpContextAccessor _httpContextAccessor;
    private static readonly JsonSerializerOptions JsonOptions = new()
    {
        PropertyNamingPolicy = JsonNamingPolicy.CamelCase
    };

    public CartService(BrasilBurgerDbContext db, IHttpContextAccessor httpContextAccessor)
    {
        _db = db;
        _httpContextAccessor = httpContextAccessor;
    }

    public async Task AddAsync(string type, int id)
    {
        var cart = await GetCartAsync();
        var existing = cart.FirstOrDefault(c => c.Type == type && c.Id == id);
        if (existing != null)
        {
            existing.Quantity += 1;
        }
        else
        {
            var product = await ResolveProductAsync(type, id);
            if (product == null) return;
            cart.Add(product);
        }
        SaveCart(cart);
    }

    public async Task<List<CartItemViewModel>> GetCartAsync()
    {
        var session = _httpContextAccessor.HttpContext?.Session;
        if (session == null) return new List<CartItemViewModel>();

        var raw = session.GetString(SessionKeys.Cart);
        if (string.IsNullOrEmpty(raw)) return new List<CartItemViewModel>();

        var items = JsonSerializer.Deserialize<List<CartItemViewModel>>(raw, JsonOptions);
        return items ?? new List<CartItemViewModel>();
    }

    public void UpdateQuantity(int index, int quantity)
    {
        var cart = GetCartAsync().GetAwaiter().GetResult();
        if (index >= 0 && index < cart.Count)
        {
            cart[index].Quantity = Math.Max(1, quantity);
            SaveCart(cart);
        }
    }

    public void Remove(int index)
    {
        var cart = GetCartAsync().GetAwaiter().GetResult();
        if (index >= 0 && index < cart.Count)
        {
            cart.RemoveAt(index);
            SaveCart(cart);
        }
    }

    public void Clear()
    {
        _httpContextAccessor.HttpContext?.Session.Remove(SessionKeys.Cart);
    }

    private void SaveCart(List<CartItemViewModel> cart)
    {
        var session = _httpContextAccessor.HttpContext?.Session;
        if (session == null) return;
        var raw = JsonSerializer.Serialize(cart, JsonOptions);
        session.SetString(SessionKeys.Cart, raw);
    }

    private async Task<CartItemViewModel?> ResolveProductAsync(string type, int id)
    {
        switch (type)
        {
            case "burger":
                var burger = await _db.Burgers.FirstOrDefaultAsync(b => b.Id == id && !b.Archive);
                return burger == null ? null : new CartItemViewModel
                {
                    Type = "burger",
                    Id = burger.Id,
                    Name = burger.Nom,
                    Description = "Burger",
                    Price = burger.Prix,
                    Image = burger.Image,
                    Quantity = 1
                };
            case "menu":
                var menu = await _db.Menus
                    .Include(m => m.Burger)
                    .Include(m => m.Boisson)
                    .Include(m => m.Frite)
                    .FirstOrDefaultAsync(m => m.Id == id && !m.Archive);
                return menu == null ? null : new CartItemViewModel
                {
                    Type = "menu",
                    Id = menu.Id,
                    Name = menu.Nom,
                    Description = $"Menu avec {menu.Burger?.Nom}",
                    Price = (menu.Burger?.Prix ?? 0) + (menu.Boisson?.Prix ?? 0) + (menu.Frite?.Prix ?? 0),
                    Image = menu.Image,
                    Quantity = 1
                };
            case "complement":
                var complement = await _db.Complements.FirstOrDefaultAsync(c => c.Id == id && !c.Archive);
                return complement == null ? null : new CartItemViewModel
                {
                    Type = "complement",
                    Id = complement.Id,
                    Name = complement.Nom,
                    Description = complement.Type,
                    Price = complement.Prix,
                    Image = complement.Image,
                    Quantity = 1
                };
            default:
                return null;
        }
    }
}





