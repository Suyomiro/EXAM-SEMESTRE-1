<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
#[ORM\Table(name: 'menus')]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'nom', type: 'string', length: 100)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Burger::class)]
    #[ORM\JoinColumn(name: 'burger_id', referencedColumnName: 'id', nullable: false)]
    private Burger $burger;

    #[ORM\ManyToOne(targetEntity: Complement::class)]
    #[ORM\JoinColumn(name: 'boisson_id', referencedColumnName: 'id', nullable: false)]
    private Complement $boisson;

    #[ORM\ManyToOne(targetEntity: Complement::class)]
    #[ORM\JoinColumn(name: 'frite_id', referencedColumnName: 'id', nullable: false)]
    private Complement $frite;

    #[ORM\Column(name: 'image', type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(name: 'archive', type: 'boolean')]
    private bool $archive = false;

    // transient/compatibility properties used by templates
    private ?string $description = null;
    private bool $available = true;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getBurger(): Burger
    {
        return $this->burger;
    }

    public function setBurger(Burger $burger): self
    {
        $this->burger = $burger;
        return $this;
    }

    public function getBoisson(): Complement
    {
        return $this->boisson;
    }

    public function setBoisson(Complement $boisson): self
    {
        $this->boisson = $boisson;
        return $this;
    }

    public function getFrite(): Complement
    {
        return $this->frite;
    }

    public function setFrite(Complement $frite): self
    {
        $this->frite = $frite;
        return $this;
    }

    public function isArchived(): bool
    {
        return $this->archive;
    }

    public function setArchive(bool $archive): self
    {
        $this->archive = $archive;
        return $this;
    }
}


