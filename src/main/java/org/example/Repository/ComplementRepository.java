package org.example.Repository;

import org.example.Entity.Complement;
import org.example.DatabaseConnection;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class ComplementRepository {
    public void add(Complement complement) throws SQLException {
        String sql = "INSERT INTO complements (nom, prix, image, type, archive) VALUES (?, ?, ?, ?, ?)";
        try (Connection conn = DatabaseConnection.getConnection(); PreparedStatement ps = conn.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {
            ps.setString(1, complement.getNom());
            ps.setDouble(2, complement.getPrix());
            ps.setString(3, complement.getImage());
            ps.setString(4, complement.getType());
            ps.setBoolean(5, complement.isArchive());
            ps.executeUpdate();
            try (ResultSet rs = ps.getGeneratedKeys()) {
                if (rs.next()) complement.setId(rs.getInt(1));
            }
        }
    }

    public void update(Complement complement) throws SQLException {
        String sql = "UPDATE complements SET nom = ?, prix = ?, image = ?, type = ?, archive = ? WHERE id = ?";
        try (Connection conn = DatabaseConnection.getConnection(); PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setString(1, complement.getNom());
            ps.setDouble(2, complement.getPrix());
            ps.setString(3, complement.getImage());
            ps.setString(4, complement.getType());
            ps.setBoolean(5, complement.isArchive());
            ps.setInt(6, complement.getId());
            ps.executeUpdate();
        }
    }

    public List<Complement> findAll() throws SQLException {
        List<Complement> complements = new ArrayList<>();
        String sql = "SELECT * FROM complements WHERE archive = FALSE";
        try (Connection conn = DatabaseConnection.getConnection(); Statement stmt = conn.createStatement(); ResultSet rs = stmt.executeQuery(sql)) {
            while (rs.next()) {
                Complement c = new Complement();
                c.setId(rs.getInt("id"));
                c.setNom(rs.getString("nom"));
                c.setPrix(rs.getDouble("prix"));
                c.setImage(rs.getString("image"));
                c.setType(rs.getString("type"));
                c.setArchive(rs.getBoolean("archive"));
                complements.add(c);
            }
        }
        return complements;
    }

    public Complement findById(int id) throws SQLException {
        Complement complement = null;
        String sql = "SELECT * FROM complements WHERE id = ?";
        try (Connection conn = DatabaseConnection.getConnection(); PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setInt(1, id);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    complement = new Complement();
                    complement.setId(rs.getInt("id"));
                    complement.setNom(rs.getString("nom"));
                    complement.setPrix(rs.getDouble("prix"));
                    complement.setImage(rs.getString("image"));
                    complement.setType(rs.getString("type"));
                    complement.setArchive(rs.getBoolean("archive"));
                }
            }
        }
        return complement;
    }
}