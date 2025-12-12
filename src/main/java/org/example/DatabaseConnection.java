package org.example;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class DatabaseConnection {
    private static final String URL = "jdbc:postgresql://ep-soft-shape-ad6f66ek-pooler.c-2.us-east-1.aws.neon.tech:5432/brasil_burger_db?sslmode=require";
    private static final String USER = "neondb_owner";
    private static final String PASSWORD = "npg_wibKs18YEvxj";

    public static Connection getConnection() throws SQLException {
        return DriverManager.getConnection(URL, USER, PASSWORD);
    }
}
