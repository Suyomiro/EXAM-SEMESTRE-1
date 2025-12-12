package org.example;

import org.example.Controller.*;
import java.sql.SQLException;
import java.util.Scanner;

public class Main {
    private static final Scanner scanner = new Scanner(System.in);

    public static void main(String[] args) {
        while (true) {
            afficherMenuPrincipal();
            int choix = lireEntier("Votre choix : ");

            if (choix == 0) {
                System.out.println("Merci et a bientot chez Brasil Burger.");
                break;
            }

            if (choix == 1) {
                gestionnaireMenu();
            } else {
                System.out.println("Choix invalide.");
            }
        }
        scanner.close();
    }

    private static void afficherMenuPrincipal() {
        System.out.println("BRASIL BURGER");
        System.out.println("1. Gestion des produits");
        System.out.println("0. Quitter");
        System.out.print("→ ");
    }

    private static void gestionnaireMenu() {
        BurgerController burgerCtrl = new BurgerController();
        ComplementController compCtrl = new ComplementController();
        MenuController menuCtrl = new MenuController();

        while (true) {
            System.out.println("GESTION PRODUITS");
            System.out.println("1. Burgers");
            System.out.println("2. Complements");
            System.out.println("3. Menus");
            System.out.println("0. Retour");
            System.out.print("→ ");

            int choix = lireEntier("");

            if (choix == 0) {
                return;
            }

            try {
                switch (choix) {
                    case 1:
                        gererProduit(burgerCtrl, "Burger");
                        break;
                    case 2:
                        gererProduit(compCtrl, "Complement");
                        break;
                    case 3:
                        gererProduit(menuCtrl, "Menu");
                        break;
                    default:
                        System.out.println("Choix invalide.");
                }
            } catch (SQLException e) {
                System.out.println("Erreur base de donnees : " + e.getMessage());
            }
        }
    }

    private static void gererProduit(Object ctrl, String nom) throws SQLException {
        System.out.println("Gestion " + nom);
        System.out.println("1. Ajouter");
        System.out.println("2. Modifier");
        System.out.println("3. Archiver");
        System.out.println("4. Lister");
        System.out.println("5. Detail");
        System.out.print("→ ");

        int act = lireEntier("");

        if (act == 4) {
            if (ctrl instanceof BurgerController c) c.list();
            if (ctrl instanceof ComplementController c) c.list();
            if (ctrl instanceof MenuController c) c.list();
            return;
        }

        int id = (act == 1) ? 0 : lireEntier("ID → ");

        if (ctrl instanceof BurgerController c) {
            if (act == 1) c.add();
            if (act == 2) c.update(id);
            if (act == 3) c.archive(id);
            if (act == 5) c.details(id);
        }

        if (ctrl instanceof ComplementController c) {
            if (act == 1) c.add();
            if (act == 2) c.update(id);
            if (act == 3) c.archive(id);
            if (act == 5) c.details(id);
        }

        if (ctrl instanceof MenuController c) {
            if (act == 1) c.add();
            if (act == 2) c.update(id);
            if (act == 3) c.archive(id);
            if (act == 5) c.details(id);
        }
    }

    private static int lireEntier(String message) {
        while (true) {
            System.out.print(message);
            try {
                return Integer.parseInt(scanner.nextLine().trim());
            } catch (NumberFormatException e) {
                System.out.println("Nombre invalide.");
            }
        }
    }
}
