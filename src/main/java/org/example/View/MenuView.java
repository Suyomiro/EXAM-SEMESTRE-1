package org.example.View;

import Entity.Menu;
import org.example.Service.MenuService;
import java.util.List;
import java.util.Scanner;

public class MenuView {
    private Scanner scanner = new Scanner(System.in);

    public Menu getInput() {
        Menu m = new Menu();
        System.out.print("Nom du menu: ");
        m.setNom(scanner.nextLine());
        System.out.print("ID burger: ");
        m.setBurgerId(scanner.nextInt());
        System.out.print("ID boisson: ");
        m.setBoissonId(scanner.nextInt());
        System.out.print("ID frite: ");
        m.setFriteId(scanner.nextInt());
        scanner.nextLine();
        System.out.print("Image (URL): ");
        m.setImage(scanner.nextLine());
        return m;
    }

    public void updateInput(Menu m) {
        System.out.print("Nouveau nom (" + m.getNom() + "): ");
        String nom = scanner.nextLine();
        if (!nom.isEmpty()) m.setNom(nom);
        System.out.print("Nouveau ID burger (" + m.getBurgerId() + "): ");
        String burgerStr = scanner.nextLine();
        if (!burgerStr.isEmpty()) m.setBurgerId(Integer.parseInt(burgerStr));
        System.out.print("Nouveau ID boisson (" + m.getBoissonId() + "): ");
        String boissonStr = scanner.nextLine();
        if (!boissonStr.isEmpty()) m.setBoissonId(Integer.parseInt(boissonStr));
        System.out.print("Nouveau ID frite (" + m.getFriteId() + "): ");
        String friteStr = scanner.nextLine();
        if (!friteStr.isEmpty()) m.setFriteId(Integer.parseInt(friteStr));
        System.out.print("Nouvelle image (" + m.getImage() + "): ");
        String image = scanner.nextLine();
        if (!image.isEmpty()) m.setImage(image);
    }

    public void display(List<Menu> menus, MenuService service) throws java.sql.SQLException {
        System.out.println("Liste des menus:");
        for (Menu m : menus) {
            double prix = service.calculPrix(m);
            System.out.println(m.getId() + " - " + m.getNom() + " : " + prix);
        }
    }

    public void displayDetails(Menu m, double prix) {
        if (m != null) {
            System.out.println("Détails du menu:");
            System.out.println("Nom: " + m.getNom());
            System.out.println("Burger ID: " + m.getBurgerId());
            System.out.println("Boisson ID: " + m.getBoissonId());
            System.out.println("Frite ID: " + m.getFriteId());
            System.out.println("Prix calculé: " + prix);
            System.out.println("Image: " + m.getImage());
        } else {
            System.out.println("Menu non trouvé.");
        }
    }

    public void displayMessage(String msg) {
        System.out.println(msg);
    }
}