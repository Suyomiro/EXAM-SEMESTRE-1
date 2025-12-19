using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace Brasil_Burger_Client.Models;

public class Commande
{
    [Key]
    [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
    [Column("id")]
    public int Id { get; set; }

    [ForeignKey(nameof(Client))]
    [Column("client_id")]
    public int ClientId { get; set; }
    public Client? Client { get; set; }

    [Column("date_commande")]
    public DateTime DateCommande { get; set; } = DateTime.UtcNow;

    [MaxLength(20)]
    [Column("etat")]
    public string Etat { get; set; } = "EN_COURS";

    [Required, MaxLength(20)]
    [Column("type")]
    public string Type { get; set; } = string.Empty;

    [ForeignKey(nameof(Zone))]
    [Column("zone_id")]
    public int? ZoneId { get; set; }
    public Zone? Zone { get; set; }

    [ForeignKey(nameof(Livreur))]
    [Column("livreur_id")]
    public int? LivreurId { get; set; }
    public Livreur? Livreur { get; set; }

    [Column("total", TypeName = "numeric(10,2)")]
    public decimal Total { get; set; }

    public Paiement? Paiement { get; set; }

    public ICollection<LigneCommande> Lignes { get; set; } = new List<LigneCommande>();
}


