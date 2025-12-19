using Brasil_Burger_Client.Data;
using Brasil_Burger_Client.Models;
using Brasil_Burger_Client.Models.ViewModels;
using Brasil_Burger_Client.Services;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using BCrypt.Net;

namespace Brasil_Burger_Client.Controllers;

public class AuthController : Controller
{
    private readonly BrasilBurgerDbContext _db;

    public AuthController(BrasilBurgerDbContext db)
    {
        _db = db;
    }

    [HttpGet]
    public IActionResult Login(bool isSignup = false)
    {
        return View(new AuthViewModel { IsSignup = isSignup });
    }

    [HttpPost]
    public async Task<IActionResult> Login(AuthViewModel model)
    {
        if (!ModelState.IsValid)
        {
            return View(model);
        }

        if (model.IsSignup == true)
        {
            // Register
            var exists = await _db.Clients.AnyAsync(c => c.Email == model.Email);
            if (exists)
            {
                ModelState.AddModelError("", "Un compte existe déjà avec cet email.");
                return View(model);
            }

            var client = new Client
            {
                Email = model.Email,
                Password = BCrypt.Net.BCrypt.HashPassword(model.Password),
                Nom = model.Nom ?? "Client",
                Prenom = model.Prenom ?? "",
                Telephone = model.Telephone ?? ""
            };
            _db.Clients.Add(client);
            await _db.SaveChangesAsync();
            SetSession(client);
            return RedirectToAction("Index", "Catalog");
        }
        else
        {
            // Login
            var client = await _db.Clients.FirstOrDefaultAsync(c => c.Email == model.Email);
            if (client == null)
            {
                ModelState.AddModelError("", "Identifiants invalides.");
                return View(model);
            }

            var passwordMatches = false;
            if (!string.IsNullOrEmpty(client.Password))
            {
                try
                {
                    passwordMatches = BCrypt.Net.BCrypt.Verify(model.Password, client.Password);
                }
                catch
                {
                    passwordMatches = client.Password == model.Password;
                }
            }

            if (!passwordMatches)
            {
                ModelState.AddModelError("", "Identifiants invalides.");
                return View(model);
            }

            SetSession(client);
            return RedirectToAction("Index", "Catalog");
        }
    }

    [HttpPost]
    public IActionResult Logout()
    {
        HttpContext.Session.Remove(SessionKeys.ClientId);
        HttpContext.Session.Remove(SessionKeys.ClientName);
        return RedirectToAction("Index", "Catalog");
    }

    private void SetSession(Client client)
    {
        HttpContext.Session.SetInt32(SessionKeys.ClientId, client.Id);
        HttpContext.Session.SetString(SessionKeys.ClientName, $"{client.Prenom} {client.Nom}".Trim());
    }
}

