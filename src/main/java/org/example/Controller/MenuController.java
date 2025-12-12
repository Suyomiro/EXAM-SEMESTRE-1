package org.example.Controller;

import org.example.Service.MenuService;
import org.example.View.MenuView;
import Entity.Menu;
import java.sql.SQLException;

public class MenuController {
    private MenuService service = new MenuService();
    private MenuView view = new MenuView();

    public void add() throws SQLException {
        Menu menu = view.getInput();
        service.addMenu(menu);
        view.displayMessage("Menu ajouté.");
    }

    public void update(int id) throws SQLException {
        Menu menu = service.getMenuById(id);
        if (menu != null) {
            view.updateInput(menu);
            service.updateMenu(menu);
            view.displayMessage("Menu mis à jour.");
        }
    }

    public void archive(int id) throws SQLException {
        service.archiveMenu(id);
        view.displayMessage("Menu archivé.");
    }

    public void list() throws SQLException {
        view.display(service.getAllMenus(), service);
    }

    public void details(int id) throws SQLException {
        Menu menu = service.getMenuById(id);
        view.displayDetails(menu, service.calculPrix(menu));
    }
}