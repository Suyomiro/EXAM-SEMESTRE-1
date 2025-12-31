<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Paiement;
use App\Entity\Client;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'commandes')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'id', nullable: false)]
    private Client $customer;

    #[ORM\Column(name: 'type', length: 20)]
    private string $type; // sur-place | à-emporter | livraison

    #[ORM\Column(name: 'etat', length: 20)]
    private string $status; // en-cours | validée | terminée | annulée

    #[ORM\Column(name: 'total', type: 'decimal', precision: 10, scale: 2)]
    private string $total;

    #[ORM\Column(name: 'date_commande', type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(name: 'adresse', type: 'string', length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\ManyToOne(targetEntity: Zone::class)]
    #[ORM\JoinColumn(name: 'zone_id', referencedColumnName: 'id', nullable: true)]
    private ?Zone $zone = null;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Livreur::class)]
    #[ORM\JoinColumn(name: 'livreur_id', referencedColumnName: 'id', nullable: true)]
    private ?\App\Entity\Livreur $livreur = null;

    #[ORM\OneToOne(mappedBy: 'commande', targetEntity: Paiement::class, cascade: ['persist', 'remove'])]
    private ?Paiement $paiement = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: OrderItem::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): Client
    {
        return $this->customer;
    }

    public function setCustomer(Client $customer): self
    {
        $this->customer = $customer;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getTotal(): string
    {
        return $this->total;
    }

    public function setTotal(string $total): self
    {
        $this->total = $total;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getDeliveryPerson(): ?string
    {
        if ($this->livreur) {
            return trim($this->livreur->getPrenom() . ' ' . $this->livreur->getNom());
        }
        return null;
    }

    public function getLivreur(): ?\App\Entity\Livreur
    {
        return $this->livreur;
    }

    public function setLivreur(?\App\Entity\Livreur $livreur): self
    {
        $this->livreur = $livreur;
        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paiement ? $this->paiement->getMethode() : null;
    }

    public function setZone(?Zone $zone): self
    {
        $this->zone = $zone;
        return $this;
    }

    

    /**
     * @return Collection<int, OrderItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setCommande($this);
        }

        return $this;
    }

    public function removeItem(OrderItem $item): self
    {
        if ($this->items->removeElement($item)) {
            if ($item->getCommande() === $this) {
                $item->setCommande(null);
            }
        }

        return $this;
    }
}


