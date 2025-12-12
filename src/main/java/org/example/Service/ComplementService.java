package org.example.Service;

import org.example.Repository.ComplementRepository;
import org.example.Entity.Complement;
import java.sql.SQLException;
import java.util.List;

public class ComplementService {
    private ComplementRepository repo = new ComplementRepository();

    public void addComplement(Complement complement) throws SQLException {
        repo.add(complement);
    }

    public void updateComplement(Complement complement) throws SQLException {
        repo.update(complement);
    }

    public void archiveComplement(int id) throws SQLException {
        Complement c = repo.findById(id);
        if (c != null) {
            c.setArchive(true);
            repo.update(c);
        }
    }

    public List<Complement> getAllComplements() throws SQLException {
        return repo.findAll();
    }

    public Complement getComplementById(int id) throws SQLException {
        return repo.findById(id);
    }
}