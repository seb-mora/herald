<?php

namespace App\Entity;

use App\Repository\EquipChantierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipChantierRepository::class)]
class EquipChantier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'chantiers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipes $fk_equipe = null;

    #[ORM\ManyToOne(inversedBy: 'equipes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Chantiers $fk_chantier = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_in = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_out = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFkEquipe(): ?Equipes
    {
        return $this->fk_equipe;
    }

    public function setFkEquipe(?Equipes $fk_equipe): static
    {
        $this->fk_equipe = $fk_equipe;

        return $this;
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

    public function getDateIn(): ?\DateTimeInterface
    {
        return $this->date_in;
    }

    public function setDateIn(?\DateTimeInterface $date_in): static
    {
        $this->date_in = $date_in;

        return $this;
    }

    public function getDateOut(): ?\DateTimeInterface
    {
        return $this->date_out;
    }

    public function setDateOut(?\DateTimeInterface $date_out): static
    {
        $this->date_out = $date_out;

        return $this;
    }
}
