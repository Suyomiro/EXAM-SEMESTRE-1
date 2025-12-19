using Brasil_Burger_Client.Data;
using Brasil_Burger_Client.Models;
using Brasil_Burger_Client.Models.ViewModels;
using Brasil_Burger_Client.Services;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;

namespace Brasil_Burger_Client.Controllers;

public class CheckoutController : Controller
{
    private readonly BrasilBurgerDbContext _db;
    private readonly CartService _cartService;

    public CheckoutController(BrasilBurgerDbContext db, CartService cartService)
    {
        _db = db;
        _cartService = cartService;
    }

    public async Task<IActionResult> Index()
    {
        var clientId = HttpContext.Session.GetInt32(SessionKeys.ClientId);
        if (clientId is null)
        {
            TempData["Error"] = "Connectez-vous pour finaliser votre commande.";
            return RedirectToAction("Login", "Auth");
        }
        try
        {
            var items = await _cartService.GetCartAsync();
            var zones = await _db.Zones.ToListAsync();
            var vm = new CheckoutViewModel
            {
                Items = items,
                Subtotal = items.Sum(i => i.Price * i.Quantity),
                Zones = zones
            };
            return View(vm);
        }
        catch (Exception)
        {
            TempData["Error"] = "Impossible de contacter la base de données pour récupérer les zones. Veuillez réessayer plus tard.";
            return RedirectToAction("Index", "Cart");
        }
    }

    [HttpPost]
    public async Task<IActionResult> Confirm(CheckoutViewModel model)
    {
        var clientId = HttpContext.Session.GetInt32(SessionKeys.ClientId);
        if (clientId is null)
        {
            TempData["Error"] = "Veuillez vous connecter pour finaliser la commande.";
            return RedirectToAction("Login", "Auth");
        }

        var items = await _cartService.GetCartAsync();
        if (!items.Any())
        {
            TempData["Error"] = "Votre panier est vide.";
            return RedirectToAction("Index", "Catalog");
        }

        decimal deliveryFee = 0;
        Zone? zone = null;
        try
        {
            if (model.OrderType == "livraison" && model.ZoneId.HasValue)
            {
                zone = await _db.Zones.FindAsync(model.ZoneId.Value);
                deliveryFee = zone?.Prix ?? 0;
            }
        }
        catch (Exception)
        {
            TempData["Error"] = "Impossible de contacter la base de données pour valider la zone de livraison. Veuillez réessayer plus tard.";
            return RedirectToAction("Index", "Cart");
        }

        var commande = new Commande
        {
            ClientId = clientId.Value,
            Type = model.OrderType,
            ZoneId = model.OrderType == "livraison" ? model.ZoneId : null,
            Total = items.Sum(i => i.Price * i.Quantity) + deliveryFee,
            Etat = "EN_COURS",
            DateCommande = DateTime.UtcNow
        };

        foreach (var item in items)
        {
            commande.Lignes.Add(new LigneCommande
            {
                BurgerId = item.Type == "burger" ? item.Id : null,
                MenuId = item.Type == "menu" ? item.Id : null,
                ComplementId = item.Type == "complement" ? item.Id : null,
                Quantite = item.Quantity,
                PrixUnitaire = item.Price
            });
        }

        try
        {
            _db.Commandes.Add(commande);
            await _db.SaveChangesAsync();
        }
        catch (Exception)
        {
            TempData["Error"] = "Impossible d'enregistrer la commande (erreur de connexion à la base). Réessayez plus tard.";
            return RedirectToAction("Index", "Cart");
        }

        // Enregistrer le paiement associé (normaliser la méthode)
        var method = (model.PaymentMethod ?? string.Empty).Trim();
        if (string.Equals(method, "Espèces", StringComparison.OrdinalIgnoreCase) || string.Equals(method, "Espèces", StringComparison.CurrentCultureIgnoreCase))
        {
            method = "Especes";
        }
        else if (string.Equals(method, "OM", StringComparison.OrdinalIgnoreCase))
        {
            method = "OM";
        }
        else
        {
            method = "Wave";
        }

        var paiement = new Paiement
        {
            CommandeId = commande.Id,
            Montant = commande.Total,
            Methode = method,
            DatePaiement = DateTime.UtcNow
        };
        try
        {
            _db.Paiements.Add(paiement);
            await _db.SaveChangesAsync();
        }
        catch (Exception)
        {
            TempData["Error"] = "La commande a été enregistrée mais le paiement n'a pas pu être sauvegardé (erreur de connexion). Contactez le support si le montant a été débité.";
            return RedirectToAction("Index", "Orders");
        }

        _cartService.Clear();
        TempData["Success"] = "Commande et paiement enregistrés avec succès.";
        return RedirectToAction("Index", "Orders");
    }
}


