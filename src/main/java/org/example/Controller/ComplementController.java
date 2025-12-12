package org.example.Controller;

import org.example.Service.ComplementService;
import org.example.View.ComplementView;
import org.example.Entity.Complement;
import java.sql.SQLException;

public class ComplementController {
    private ComplementService service = new ComplementService();
    private ComplementView view = new ComplementView();

    public void add() throws SQLException {
        Complement complement = view.getInput();
        service.addComplement(complement);
        view.displayMessage("Complément ajouté.");
    }

    public void update(int id) throws SQLException {
        Complement complement = service.getComplementById(id);
        if (complement != null) {
            view.updateInput(complement);
            service.updateComplement(complement);
            view.displayMessage("Complément mis à jour.");
        }
    }

    public void archive(int id) throws SQLException {
        service.archiveComplement(id);
        view.displayMessage("Complément archivé.");
    }

    public void list() throws SQLException {
        view.display(service.getAllComplements());
    }

    public void details(int id) throws SQLException {
        view.displayDetails(service.getComplementById(id));
    }
}