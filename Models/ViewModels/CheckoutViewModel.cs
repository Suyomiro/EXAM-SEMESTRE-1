using System.Linq;

namespace Brasil_Burger_Client.Models.ViewModels;

public class CheckoutViewModel
{
    public IEnumerable<CartItemViewModel> Items { get; set; } = Enumerable.Empty<CartItemViewModel>();
    public decimal Subtotal { get; set; }
    public decimal DeliveryFee { get; set; }
    public decimal Total => Subtotal + DeliveryFee;
    public IEnumerable<Zone> Zones { get; set; } = Enumerable.Empty<Zone>();

    public string OrderType { get; set; } = "livraison";
    public string PaymentMethod { get; set; } = "Wave";
    public string? Address { get; set; }
    public int? ZoneId { get; set; }
    public string? Notes { get; set; }
}

