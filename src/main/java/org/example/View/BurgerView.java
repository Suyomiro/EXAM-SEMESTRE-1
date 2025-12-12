package org.example.View;

import org.example.Entity.Burger;
import java.util.List;
import java.util.Scanner;

public class BurgerView {
    private Scanner scanner = new Scanner(System.in);

    public Burger getInput() {
        Burger b = new Burger();
        System.out.print("Nom du burger: ");
        b.setNom(scanner.nextLine());
        System.out.print("Prix: ");
        b.setPrix(scanner.nextDouble());
        scanner.nextLine();
        System.out.print("Image (URL): ");
        b.setImage(scanner.nextLine());
        return b;
    }

    public void updateInput(Burger b) {
        System.out.print("Nouveau nom (" + b.getNom() + "): ");
        String nom = scanner.nextLine();
        if (!nom.isEmpty()) b.setNom(nom);
        System.out.print("Nouveau prix (" + b.getPrix() + "): ");
        String prixStr = scanner.nextLine();
        if (!prixStr.isEmpty()) b.setPrix(Double.parseDouble(prixStr));
        System.out.print("Nouvelle image (" + b.getImage() + "): ");
        String image = scanner.nextLine();
        if (!image.isEmpty()) b.setImage(image);
    }

    public void display(List<Burger> burgers) {
        System.out.println("Liste des burgers:");
        for (Burger b : burgers) {
            System.out.println(b.getId() + " - " + b.getNom() + " : " + b.getPrix());
        }
    }

    public void displayDetails(Burger b) {
        if (b != null) {
            System.out.println("Détails du burger:");
            System.out.println("Nom: " + b.getNom());
            System.out.println("Prix: " + b.getPrix());
            System.out.println("Image: " + b.getImage());
        } else {
            System.out.println("Burger non trouvé.");
        }
    }

    public void displayMessage(String msg) {
        System.out.println(msg);
    }
}