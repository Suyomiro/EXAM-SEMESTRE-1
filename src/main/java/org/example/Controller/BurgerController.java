package org.example.Controller;

import org.example.Service.BurgerService;
import org.example.View.BurgerView;
import org.example.Entity.Burger;
import java.sql.SQLException;

public class BurgerController {
    private BurgerService service = new BurgerService();
    private BurgerView view = new BurgerView();

    public void add() throws SQLException {
        Burger burger = view.getInput();
        service.addBurger(burger);
        view.displayMessage("Burger ajouté.");
    }

    public void update(int id) throws SQLException {
        Burger burger = service.getBurgerById(id);
        if (burger != null) {
            view.updateInput(burger);
            service.updateBurger(burger);
            view.displayMessage("Burger mis à jour.");
        }
    }

    public void archive(int id) throws SQLException {
        service.archiveBurger(id);
        view.displayMessage("Burger archivé.");
    }

    public void list() throws SQLException {
        view.display(service.getAllBurgers());
    }

    public void details(int id) throws SQLException {
        view.displayDetails(service.getBurgerById(id));
    }
}