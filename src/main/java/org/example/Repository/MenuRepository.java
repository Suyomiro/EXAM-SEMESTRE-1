package org.example.Repository;

import Entity.Menu;
import org.example.DatabaseConnection;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class MenuRepository {
    public void add(Menu menu) throws SQLException {
        String sql = "INSERT INTO menus (nom, burger_id, boisson_id, frite_id, image, archive) VALUES (?, ?, ?, ?, ?, ?)";
        try (Connection conn = DatabaseConnection.getConnection(); PreparedStatement ps = conn.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {
            ps.setString(1, menu.getNom());
            ps.setInt(2, menu.getBurgerId());
            ps.setInt(3, menu.getBoissonId());
            ps.setInt(4, menu.getFriteId());
            ps.setString(5, menu.getImage());
            ps.setBoolean(6, menu.isArchive());
            ps.executeUpdate();
            try (ResultSet rs = ps.getGeneratedKeys()) {
                if (rs.next()) menu.setId(rs.getInt(1));
            }
        }
    }

    public void update(Menu menu) throws SQLException {
        String sql = "UPDATE menus SET nom = ?, burger_id = ?, boisson_id = ?, frite_id = ?, image = ?, archive = ? WHERE id = ?";
        try (Connection conn = DatabaseConnection.getConnection(); PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setString(1, menu.getNom());
            ps.setInt(2, menu.getBurgerId());
            ps.setInt(3, menu.getBoissonId());
            ps.setInt(4, menu.getFriteId());
            ps.setString(5, menu.getImage());
            ps.setBoolean(6, menu.isArchive());
            ps.setInt(7, menu.getId());
            ps.executeUpdate();
        }
    }

    public List<Menu> findAll() throws SQLException {
        List<Menu> menus = new ArrayList<>();
        String sql = "SELECT * FROM menus WHERE archive = FALSE";
        try (Connection conn = DatabaseConnection.getConnection(); Statement stmt = conn.createStatement(); ResultSet rs = stmt.executeQuery(sql)) {
            while (rs.next()) {
                Menu m = new Menu();
                m.setId(rs.getInt("id"));
                m.setNom(rs.getString("nom"));
                m.setBurgerId(rs.getInt("burger_id"));
                m.setBoissonId(rs.getInt("boisson_id"));
                m.setFriteId(rs.getInt("frite_id"));
                m.setImage(rs.getString("image"));
                m.setArchive(rs.getBoolean("archive"));
                menus.add(m);
            }
        }
        return menus;
    }

    public Menu findById(int id) throws SQLException {
        Menu menu = null;
        String sql = "SELECT * FROM menus WHERE id = ?";
        try (Connection conn = DatabaseConnection.getConnection(); PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setInt(1, id);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    menu = new Menu();
                    menu.setId(rs.getInt("id"));
                    menu.setNom(rs.getString("nom"));
                    menu.setBurgerId(rs.getInt("burger_id"));
                    menu.setBoissonId(rs.getInt("boisson_id"));
                    menu.setFriteId(rs.getInt("frite_id"));
                    menu.setImage(rs.getString("image"));
                    menu.setArchive(rs.getBoolean("archive"));
                }
            }
        }
        return menu;
    }
}