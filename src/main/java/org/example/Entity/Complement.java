package org.example.Entity;

public class Complement {
    private int id;
    private String nom;
    private double prix;
    private String image;
    private String type; // FRITE or BOISSON
    private boolean archive;

    public int getId() { return id; }
    public void setId(int id) { this.id = id; }
    public String getNom() { return nom; }
    public void setNom(String nom) { this.nom = nom; }
    public double getPrix() { return prix; }
    public void setPrix(double prix) { this.prix = prix; }
    public String getImage() { return image; }
    public void setImage(String image) { this.image = image; }
    public String getType() { return type; }
    public void setType(String type) { this.type = type; }
    public boolean isArchive() { return archive; }
    public void setArchive(boolean archive) { this.archive = archive; }
}