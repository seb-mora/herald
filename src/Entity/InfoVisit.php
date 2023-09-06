<?php

namespace App\Entity;

use App\Repository\InfoVisitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InfoVisitRepository::class)]
class InfoVisit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_show = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_hide = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDateShow(): ?\DateTimeInterface
    {
        return $this->date_show;
    }

    public function setDateShow(\DateTimeInterface $date_show): static
    {
        $this->date_show = $date_show;

        return $this;
    }

    public function getDateHide(): ?\DateTimeInterface
    {
        return $this->date_hide;
    }

    public function setDateHide(?\DateTimeInterface $date_hide): static
    {
        $this->date_hide = $date_hide;

        return $this;
    }
}
