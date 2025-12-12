package org.example.Repository;

import org.example.Entity.Burger;
import org.example.DatabaseConnection;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class BurgerRepository {
    public void add(Burger burger) throws SQLException {
        String sql = "INSERT INTO burgers (nom, prix, image, archive) VALUES (?, ?, ?, ?)";
        try (Connection conn = DatabaseConnection.getConnection(); PreparedStatement ps = conn.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {
            ps.setString(1, burger.getNom());
            ps.setDouble(2, burger.getPrix());
            ps.setString(3, burger.getImage());
            ps.setBoolean(4, burger.isArchive());
            ps.executeUpdate();
            try (ResultSet rs = ps.getGeneratedKeys()) {
                if (rs.next()) burger.setId(rs.getInt(1));
            }
        }
    }

    public void update(Burger burger) throws SQLException {
        String sql = "UPDATE burgers SET nom = ?, prix = ?, image = ?, archive = ? WHERE id = ?";
        try (Connection conn = DatabaseConnection.getConnection(); PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setString(1, burger.getNom());
            ps.setDouble(2, burger.getPrix());
            ps.setString(3, burger.getImage());
            ps.setBoolean(4, burger.isArchive());
            ps.setInt(5, burger.getId());
            ps.executeUpdate();
        }
    }

    public List<Burger> findAll() throws SQLException {
        List<Burger> burgers = new ArrayList<>();
        String sql = "SELECT * FROM burgers WHERE archive = FALSE";
        try (Connection conn = DatabaseConnection.getConnection(); Statement stmt = conn.createStatement(); ResultSet rs = stmt.executeQuery(sql)) {
            while (rs.next()) {
                Burger b = new Burger();
                b.setId(rs.getInt("id"));
                b.setNom(rs.getString("nom"));
                b.setPrix(rs.getDouble("prix"));
                b.setImage(rs.getString("image"));
                b.setArchive(rs.getBoolean("archive"));
                burgers.add(b);
            }
        }
        return burgers;
    }

    public Burger findById(int id) throws SQLException {
        Burger burger = null;
        String sql = "SELECT * FROM burgers WHERE id = ?";
        try (Connection conn = DatabaseConnection.getConnection(); PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setInt(1, id);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    burger = new Burger();
                    burger.setId(rs.getInt("id"));
                    burger.setNom(rs.getString("nom"));
                    burger.setPrix(rs.getDouble("prix"));
                    burger.setImage(rs.getString("image"));
                    burger.setArchive(rs.getBoolean("archive"));
                }
            }
        }
        return burger;
    }
}