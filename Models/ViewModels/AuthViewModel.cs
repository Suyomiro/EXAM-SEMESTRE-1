using System.ComponentModel.DataAnnotations;

namespace Brasil_Burger_Client.Models.ViewModels;

public class AuthViewModel
{
    [Required, EmailAddress]
    public string Email { get; set; } = string.Empty;

    [Required]
    public string Password { get; set; } = string.Empty;

    [MaxLength(100)]
    public string? Nom { get; set; }

    [MaxLength(100)]
    public string? Prenom { get; set; }

    [MaxLength(20)]
    public string? Telephone { get; set; }

    public bool? IsSignup { get; set; }
}





