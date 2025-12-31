<?php

namespace App\Entity;

use App\Repository\ZoneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ZoneRepository::class)]
#[ORM\Table(name: 'zones')]
class Zone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'nom', type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(name: 'quartiers', type: 'text', nullable: true)]
    private ?string $quartiers = null;

    #[ORM\Column(name: 'prix', type: 'decimal', precision: 10, scale: 2)]
    private string $price;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }


    public function getQuartiers(): ?string
    {
        return $this->quartiers;
    }

    public function setQuartiers(?string $quartiers): self
    {
        $this->quartiers = $quartiers;
        return $this;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;
        return $this;
    }
}


