using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace Brasil_Burger_Client.Models;

public class Zone
{
    [Key]
    [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
    [Column("id")]
    public int Id { get; set; }

    [Required, MaxLength(100)]
    [Column("nom")]
    public string Nom { get; set; } = string.Empty;

    [Column("quartiers")]
    public string? Quartiers { get; set; }

    [Column("prix", TypeName = "numeric(10,2)")]
    public decimal Prix { get; set; }

    public ICollection<Commande> Commandes { get; set; } = new List<Commande>();
}


