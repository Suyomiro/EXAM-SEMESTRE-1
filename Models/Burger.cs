using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace Brasil_Burger_Client.Models;

public class Burger
{
    [Key]
    [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
    [Column("id")]
    public int Id { get; set; }

    [Required, MaxLength(100)]
    [Column("nom")]
    public string Nom { get; set; } = string.Empty;

    [Column("prix", TypeName = "numeric(10,2)")]
    public decimal Prix { get; set; }

    [MaxLength(255)]
    [Column("image")]
    public string? Image { get; set; }

    [Column("archive")]
    public bool Archive { get; set; }

    public ICollection<LigneCommande> LignesCommande { get; set; } = new List<LigneCommande>();
}


