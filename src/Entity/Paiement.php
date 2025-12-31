<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PaiementRepository;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
#[ORM\Table(name: 'paiements')]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Order::class, inversedBy: 'paiement')]
    #[ORM\JoinColumn(name: 'commande_id', referencedColumnName: 'id', nullable: false, unique: true)]
    private Order $commande;

    #[ORM\Column(name: 'date_paiement', type: 'datetime')]
    private \DateTime $datePaiement;

    #[ORM\Column(name: 'montant', type: 'decimal', precision: 10, scale: 2)]
    private string $montant;

    #[ORM\Column(name: 'methode', type: 'string', length: 10)]
    private string $methode;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommande(): Order
    {
        return $this->commande;
    }

    public function setCommande(Order $commande): self
    {
        $this->commande = $commande;
        return $this;
    }

    public function getDatePaiement(): \DateTime
    {
        return $this->datePaiement;
    }

    public function setDatePaiement(\DateTime $datePaiement): self
    {
        $this->datePaiement = $datePaiement;
        return $this;
    }

    public function getMontant(): string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;
        return $this;
    }

    public function getMethode(): string
    {
        return $this->methode;
    }

    public function setMethode(string $methode): self
    {
        $this->methode = $methode;
        return $this;
    }
}
