using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace Brasil_Burger_Client.Models;

public class Menu
{
    [Key]
    [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
    [Column("id")]
    public int Id { get; set; }

    [Required, MaxLength(100)]
    [Column("nom")]
    public string Nom { get; set; } = string.Empty;

    [ForeignKey(nameof(Burger))]
    [Column("burger_id")]
    public int BurgerId { get; set; }
    public Burger? Burger { get; set; }

    [ForeignKey(nameof(Boisson))]
    [Column("boisson_id")]
    public int BoissonId { get; set; }
    public Complement? Boisson { get; set; }

    [ForeignKey(nameof(Frite))]
    [Column("frite_id")]
    public int FriteId { get; set; }
    public Complement? Frite { get; set; }

    [MaxLength(255)]
    [Column("image")]
    public string? Image { get; set; }

    [Column("archive")]
    public bool Archive { get; set; }

    public ICollection<LigneCommande> LignesCommande { get; set; } = new List<LigneCommande>();
}


