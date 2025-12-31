<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
#[ORM\Table(name: 'lignes_commande')]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    #[ORM\JoinColumn(name: 'commande_id', referencedColumnName: 'id', nullable: false)]
    private ?Order $commande = null;

    #[ORM\ManyToOne(targetEntity: Burger::class)]
    #[ORM\JoinColumn(name: 'burger_id', referencedColumnName: 'id', nullable: true)]
    private ?Burger $burger = null;

    #[ORM\ManyToOne(targetEntity: Menu::class)]
    #[ORM\JoinColumn(name: 'menu_id', referencedColumnName: 'id', nullable: true)]
    private ?Menu $menu = null;

    #[ORM\ManyToOne(targetEntity: Complement::class)]
    #[ORM\JoinColumn(name: 'complement_id', referencedColumnName: 'id', nullable: true)]
    private ?Complement $complement = null;

    #[ORM\Column(name: 'quantite', type: 'integer')]
    private int $quantite = 1;

    #[ORM\Column(name: 'prix_unitaire', type: 'decimal', precision: 10, scale: 2)]
    private string $prixUnitaire;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommande(): ?Order
    {
        return $this->commande;
    }

    public function setCommande(?Order $commande): self
    {
        $this->commande = $commande;
        return $this;
    }

    public function getBurger(): ?Burger
    {
        return $this->burger;
    }

    public function setBurger(?Burger $burger): self
    {
        $this->burger = $burger;
        return $this;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): self
    {
        $this->menu = $menu;
        return $this;
    }

    public function getComplement(): ?Complement
    {
        return $this->complement;
    }

    public function setComplement(?Complement $complement): self
    {
        $this->complement = $complement;
        return $this;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }

    public function getPrixUnitaire(): string
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(string $prixUnitaire): self
    {
        $this->prixUnitaire = $prixUnitaire;
        return $this;
    }

    // Compatibility getters expected by existing templates/controllers
    public function getQuantity(): int
    {
        return $this->quantite;
    }

    public function getPrice(): string
    {
        return $this->prixUnitaire;
    }

    public function getName(): string
    {
        if ($this->burger) {
            return $this->burger->getName();
        }
        if ($this->menu) {
            return $this->menu->getName();
        }
        if ($this->complement) {
            return $this->complement->getName();
        }
        return '';
    }
}


