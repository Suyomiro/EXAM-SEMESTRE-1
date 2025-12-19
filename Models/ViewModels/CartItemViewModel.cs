namespace Brasil_Burger_Client.Models.ViewModels;

public class CartItemViewModel
{
    public string Type { get; set; } = string.Empty; // burger | menu | complement
    public int Id { get; set; }
    public string Name { get; set; } = string.Empty;
    public string Description { get; set; } = string.Empty;
    public decimal Price { get; set; }
    public string? Image { get; set; }
    public int Quantity { get; set; }
}





