<?php

namespace App\Entity;

use App\Repository\EquipesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipesRepository::class)]
class Equipes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 36)]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'fk_equipe', targetEntity: EquipChantier::class)]
    private Collection $chantiers;

    #[ORM\OneToMany(mappedBy: 'fk_equipe', targetEntity: Users::class)]
    private Collection $users;

    public function __construct()
    {
        $this->chantiers = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, EquipChantier>
     */
    public function getChantiers(): Collection
    {
        return $this->chantiers;
    }

    public function addChantier(EquipChantier $chantier): static
    {
        if (!$this->chantiers->contains($chantier)) {
            $this->chantiers->add($chantier);
            $chantier->setFkEquipe($this);
        }

        return $this;
    }

    public function removeChantier(EquipChantier $chantier): static
    {
        if ($this->chantiers->removeElement($chantier)) {
            // set the owning side to null (unless already changed)
            if ($chantier->getFkEquipe() === $this) {
                $chantier->setFkEquipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Users>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(Users $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setFkEquipe($this);
        }

        return $this;
    }

    public function removeUser(Users $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getFkEquipe() === $this) {
                $user->setFkEquipe(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nom;
    }
}
