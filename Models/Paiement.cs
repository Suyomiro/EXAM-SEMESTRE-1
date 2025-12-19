using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace Brasil_Burger_Client.Models;

public class Paiement
{
    [Key]
    [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
    [Column("id")]
    public int Id { get; set; }

    [ForeignKey(nameof(Commande))]
    [Column("commande_id")]
    public int CommandeId { get; set; }
    public Commande? Commande { get; set; }

    [Column("date_paiement")]
    public DateTime DatePaiement { get; set; } = DateTime.UtcNow;

    [Column("montant", TypeName = "numeric(10,2)")]
    public decimal Montant { get; set; }

    [Required, MaxLength(10)]
    [Column("methode")]
    public string Methode { get; set; } = string.Empty;
}


