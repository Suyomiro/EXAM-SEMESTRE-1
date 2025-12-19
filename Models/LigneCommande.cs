using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace Brasil_Burger_Client.Models;

public class LigneCommande
{
    [Key]
    [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
    [Column("id")]
    public int Id { get; set; }

    [ForeignKey(nameof(Commande))]
    [Column("commande_id")]
    public int CommandeId { get; set; }
    public Commande? Commande { get; set; }

    [ForeignKey(nameof(Burger))]
    [Column("burger_id")]
    public int? BurgerId { get; set; }
    public Burger? Burger { get; set; }

    [ForeignKey(nameof(Menu))]
    [Column("menu_id")]
    public int? MenuId { get; set; }
    public Menu? Menu { get; set; }

    [ForeignKey(nameof(Complement))]
    [Column("complement_id")]
    public int? ComplementId { get; set; }
    public Complement? Complement { get; set; }

    [Column("quantite")]
    public int Quantite { get; set; } = 1;

    [Column("prix_unitaire", TypeName = "numeric(10,2)")]
    public decimal PrixUnitaire { get; set; }
}


