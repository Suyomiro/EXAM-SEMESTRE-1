using Brasil_Burger_Client.Services;
using Microsoft.AspNetCore.Mvc;

namespace Brasil_Burger_Client.Controllers;

public class CartController : Controller
{
    private readonly CartService _cartService;

    public CartController(CartService cartService)
    {
        _cartService = cartService;
    }

    public async Task<IActionResult> Index()
    {
        var clientId = HttpContext.Session.GetInt32(SessionKeys.ClientId);
        if (clientId is null)
        {
            TempData["Error"] = "Connectez-vous pour accéder à votre panier.";
            return RedirectToAction("Login", "Auth");
        }

        var items = await _cartService.GetCartAsync();
        return View(items);
    }

    [HttpPost]
    public IActionResult Update(int index, int quantity)
    {
        var clientId = HttpContext.Session.GetInt32(SessionKeys.ClientId);
        if (clientId is null)
        {
            TempData["Error"] = "Connectez-vous pour modifier votre panier.";
            return RedirectToAction("Login", "Auth");
        }

        _cartService.UpdateQuantity(index, quantity);
        return RedirectToAction(nameof(Index));
    }

    [HttpPost]
    public IActionResult Remove(int index)
    {
        var clientId = HttpContext.Session.GetInt32(SessionKeys.ClientId);
        if (clientId is null)
        {
            TempData["Error"] = "Connectez-vous pour modifier votre panier.";
            return RedirectToAction("Login", "Auth");
        }

        _cartService.Remove(index);
        return RedirectToAction(nameof(Index));
    }
}


