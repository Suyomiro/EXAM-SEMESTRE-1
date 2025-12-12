package org.example.View;

import org.example.Entity.Complement;
import java.util.List;
import java.util.Scanner;

public class ComplementView {
    private Scanner scanner = new Scanner(System.in);

    public Complement getInput() {
        Complement c = new Complement();
        System.out.print("Nom du complément: ");
        c.setNom(scanner.nextLine());
        System.out.print("Prix: ");
        c.setPrix(scanner.nextDouble());
        scanner.nextLine();
        System.out.print("Image (URL): ");
        c.setImage(scanner.nextLine());
        System.out.print("Type (FRITE/BOISSON): ");
        c.setType(scanner.nextLine().toUpperCase());
        return c;
    }

    public void updateInput(Complement c) {
        System.out.print("Nouveau nom (" + c.getNom() + "): ");
        String nom = scanner.nextLine();
        if (!nom.isEmpty()) c.setNom(nom);
        System.out.print("Nouveau prix (" + c.getPrix() + "): ");
        String prixStr = scanner.nextLine();
        if (!prixStr.isEmpty()) c.setPrix(Double.parseDouble(prixStr));
        System.out.print("Nouvelle image (" + c.getImage() + "): ");
        String image = scanner.nextLine();
        if (!image.isEmpty()) c.setImage(image);
        System.out.print("Nouveau type (" + c.getType() + "): ");
        String type = scanner.nextLine();
        if (!type.isEmpty()) c.setType(type.toUpperCase());
    }

    public void display(List<Complement> complements) {
        System.out.println("Liste des compléments:");
        for (Complement c : complements) {
            System.out.println(c.getId() + " - " + c.getNom() + " (" + c.getType() + ") : " + c.getPrix());
        }
    }

    public void displayDetails(Complement c) {
        if (c != null) {
            System.out.println("Détails du complément:");
            System.out.println("Nom: " + c.getNom());
            System.out.println("Prix: " + c.getPrix());
            System.out.println("Type: " + c.getType());
            System.out.println("Image: " + c.getImage());
        } else {
            System.out.println("Complément non trouvé.");
        }
    }

    public void displayMessage(String msg) {
        System.out.println(msg);
    }
}