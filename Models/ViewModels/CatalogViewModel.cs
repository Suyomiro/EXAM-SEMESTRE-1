using System.Collections.Generic;
using System.Linq;

namespace Brasil_Burger_Client.Models.ViewModels;

public class CatalogViewModel
{
    public IEnumerable<Burger> Burgers { get; set; } = Enumerable.Empty<Burger>();
    public IEnumerable<Menu> Menus { get; set; } = Enumerable.Empty<Menu>();
    public IEnumerable<Complement> Complements { get; set; } = Enumerable.Empty<Complement>();
    public string Search { get; set; } = string.Empty;
    public string Category { get; set; } = "all";
}

