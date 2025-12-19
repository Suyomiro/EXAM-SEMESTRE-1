using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace Brasil_Burger_Client.Models;

public class Client
{
    [Key]
    [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
    [Column("id")]
    public int Id { get; set; }

    [Required, MaxLength(100)]
    [Column("nom")]
    public string Nom { get; set; } = string.Empty;

    [Required, MaxLength(100)]
    [Column("prenom")]
    public string Prenom { get; set; } = string.Empty;

    [Required, MaxLength(20)]
    [Column("telephone")]
    public string Telephone { get; set; } = string.Empty;

    [MaxLength(100)]
    [Column("email")]
    public string? Email { get; set; }

    [Required, MaxLength(255)]
    [Column("password")]
    public string Password { get; set; } = string.Empty;

    public ICollection<Commande> Commandes { get; set; } = new List<Commande>();
}


