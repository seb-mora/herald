<?php

namespace App\Entity;

use App\Repository\PhotosRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhotosRepository::class)]
class Photos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'photos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Chantiers $fk_chantier = null;

    #[ORM\Column(length: 255)]
    private ?string $lien = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $principale = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFkChantier(): ?Chantiers
    {
        return $this->fk_chantier;
    }

    public function setFkChantier(?Chantiers $fk_chantier): static
    {
        $this->fk_chantier = $fk_chantier;

        return $this;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(string $lien): static
    {
        $this->lien = $lien;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isPrincipale(): ?bool
    {
        return $this->principale;
    }

    public function setPrincipale(bool $principale): static
    {
        $this->principale = $principale;

        return $this;
    }
    public function __toString()
    {
        return $this->lien;
    }
}
