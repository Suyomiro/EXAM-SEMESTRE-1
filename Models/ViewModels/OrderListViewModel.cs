using System.Linq;

namespace Brasil_Burger_Client.Models.ViewModels;

public class OrderListViewModel
{
    public IEnumerable<Commande> Orders { get; set; } = Enumerable.Empty<Commande>();
}

