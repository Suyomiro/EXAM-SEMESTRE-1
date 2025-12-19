using Brasil_Burger_Client.Data;
using Brasil_Burger_Client.Models.ViewModels;
using Brasil_Burger_Client.Services;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;

namespace Brasil_Burger_Client.Controllers;

public class CatalogController : Controller
{
    private readonly BrasilBurgerDbContext _db;
    private readonly CartService _cartService;

    public CatalogController(BrasilBurgerDbContext db, CartService cartService)
    {
        _db = db;
        _cartService = cartService;
    }

    public async Task<IActionResult> Index(string? search, string category = "all")
    {
        try
        {
            var burgersQuery = _db.Burgers.Where(b => !b.Archive);
            var menusQuery = _db.Menus
                .Include(m => m.Burger)
                .Include(m => m.Boisson)
                .Include(m => m.Frite)
                .Where(m => !m.Archive);
            var complementsQuery = _db.Complements.Where(c => !c.Archive);

            if (!string.IsNullOrWhiteSpace(search))
            {
                burgersQuery = burgersQuery.Where(b => b.Nom.ToLower().Contains(search.ToLower()));
                menusQuery = menusQuery.Where(m => m.Nom.ToLower().Contains(search.ToLower()));
                complementsQuery = complementsQuery.Where(c => c.Nom.ToLower().Contains(search.ToLower()));
            }

            if (!string.IsNullOrWhiteSpace(category) && category != "all")
            {
                complementsQuery = complementsQuery.Where(c => c.Type == category);
            }

            var vm = new CatalogViewModel
            {
                Burgers = await burgersQuery.ToListAsync(),
                Menus = await menusQuery.ToListAsync(),
                Complements = await complementsQuery.ToListAsync(),
                Search = search ?? string.Empty,
                Category = category
            };

            return View(vm);
        }
        catch (Exception)
        {
            TempData["Error"] = "Impossible de contacter la base de données. Veuillez réessayer plus tard.";
            var emptyVm = new CatalogViewModel
            {
                Burgers = Enumerable.Empty<Models.Burger>(),
                Menus = Enumerable.Empty<Models.Menu>(),
                Complements = Enumerable.Empty<Models.Complement>(),
                Search = search ?? string.Empty,
                Category = category
            };
            return View(emptyVm);
        }
    }

    [HttpPost]
    public async Task<IActionResult> AddToCart(string type, int id)
    {
        var clientId = HttpContext.Session.GetInt32(SessionKeys.ClientId);
        if (clientId is null)
        {
            TempData["Error"] = "Connectez-vous ou inscrivez-vous avant d'ajouter au panier.";
            return RedirectToAction("Login", "Auth", new { isSignup = false });
        }

        await _cartService.AddAsync(type, id);
        return RedirectToAction(nameof(Index));
    }
}


