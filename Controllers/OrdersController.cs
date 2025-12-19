using Brasil_Burger_Client.Data;
using Brasil_Burger_Client.Models.ViewModels;
using Brasil_Burger_Client.Services;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;

namespace Brasil_Burger_Client.Controllers;

public class OrdersController : Controller
{
    private readonly BrasilBurgerDbContext _db;

    public OrdersController(BrasilBurgerDbContext db)
    {
        _db = db;
    }

    public async Task<IActionResult> Index()
    {
        var clientId = HttpContext.Session.GetInt32(SessionKeys.ClientId);
        if (clientId is null)
        {
            TempData["Error"] = "Connectez-vous pour voir vos commandes.";
            return RedirectToAction("Login", "Auth");
        }

        var orders = await _db.Commandes
            .Include(c => c.Zone)
            .Include(c => c.Lignes)
            .ThenInclude(l => l.Burger)
            .Include(c => c.Lignes)
            .ThenInclude(l => l.Menu)
            .Include(c => c.Lignes)
            .ThenInclude(l => l.Complement)
            .Where(c => c.ClientId == clientId.Value)
            .OrderByDescending(c => c.DateCommande)
            .ToListAsync();

        var vm = new OrderListViewModel
        {
            Orders = orders
        };

        return View(vm);
    }
}





