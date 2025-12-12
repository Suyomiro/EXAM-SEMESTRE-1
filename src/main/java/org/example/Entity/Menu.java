package Entity;

public class Menu {
    private int id;
    private String nom;
    private int burgerId;
    private int boissonId;
    private int friteId;
    private String image;
    private boolean archive;

    public int getId() { return id; }
    public void setId(int id) { this.id = id; }
    public String getNom() { return nom; }
    public void setNom(String nom) { this.nom = nom; }
    public int getBurgerId() { return burgerId; }
    public void setBurgerId(int burgerId) { this.burgerId = burgerId; }
    public int getBoissonId() { return boissonId; }
    public void setBoissonId(int boissonId) { this.boissonId = boissonId; }
    public int getFriteId() { return friteId; }
    public void setFriteId(int friteId) { this.friteId = friteId; }
    public String getImage() { return image; }
    public void setImage(String image) { this.image = image; }
    public boolean isArchive() { return archive; }
    public void setArchive(boolean archive) { this.archive = archive; }
}