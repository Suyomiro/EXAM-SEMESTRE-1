package org.example.Service;

import org.example.Repository.MenuRepository;
import org.example.Repository.BurgerRepository;
//import org.example.Repository.ComplementRepository;
import Entity.Menu;
import org.example.Entity.Burger;
//import org.example.Entity.Complement;
import java.sql.SQLException;
import java.util.List;

public class MenuService {
    private MenuRepository repo = new MenuRepository();
    private BurgerRepository burgerRepo = new BurgerRepository();
//    private ComplementRepository complementRepo = new ComplementRepository();

    public void addMenu(Menu menu) throws SQLException {
        repo.add(menu);
    }

    public void updateMenu(Menu menu) throws SQLException {
        repo.update(menu);
    }

    public void archiveMenu(int id) throws SQLException {
        Menu m = repo.findById(id);
        if (m != null) {
            m.setArchive(true);
            repo.update(m);
        }
    }

    public List<Menu> getAllMenus() throws SQLException {
        return repo.findAll();
    }

    public Menu getMenuById(int id) throws SQLException {
        return repo.findById(id);
    }

    public double calculPrix(Menu menu) throws SQLException {
        Burger burger = burgerRepo.findById(menu.getBurgerId());
//        Complement boisson = complementRepo.findById(menu.getBoissonId());
//        Complement frite = complementRepo.findById(menu.getFriteId());
//        if (burger != null && boisson != null && frite != null) {
//            return burger.getPrix() + boisson.getPrix() + frite.getPrix();
//        }
//        return 0;
//    }
//}