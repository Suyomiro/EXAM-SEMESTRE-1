package org.example.Service;

import org.example.Repository.BurgerRepository;
import org.example.Entity.Burger;
import java.sql.SQLException;
import java.util.List;

public class BurgerService {
    private BurgerRepository repo = new BurgerRepository();

    public void addBurger(Burger burger) throws SQLException {
        repo.add(burger);
    }

    public void updateBurger(Burger burger) throws SQLException {
        repo.update(burger);
    }

    public void archiveBurger(int id) throws SQLException {
        Burger b = repo.findById(id);
        if (b != null) {
            b.setArchive(true);
            repo.update(b);
        }
    }

    public List<Burger> getAllBurgers() throws SQLException {
        return repo.findAll();
    }

    public Burger getBurgerById(int id) throws SQLException {
        return repo.findById(id);
    }
}