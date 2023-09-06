<?php

namespace App\Entity;

use App\Repository\ChantiersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChantiersRepository::class)]
class Chantiers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 36)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'chantiers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Clients $fk_client = null;

    #[ORM\Column(length: 255)]
    private ?string $localisation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column]
    private ?int $duree_sem = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 2)]
    private ?string $montant = null;

    #[ORM\Column]
    private ?bool $facture_emise = null;

    #[ORM\Column]
    private ?bool $paiement_recu = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $retour_client = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $retour_equipe = null;

    #[ORM\Column]
    private ?bool $clos = null;

    #[ORM\OneToMany(mappedBy: 'fk_chantier', targetEntity: EquipChantier::class)]
    private Collection $equipes;

    #[ORM\OneToMany(mappedBy: 'fk_chantier', targetEntity: Photos::class)]
    private Collection $photos;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Description = null;

    public function __construct()
    {
        $this->equipes = new ArrayCollection();
        $this->photos = new ArrayCollection();
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

    public function getFkClient(): ?Clients
    {
        return $this->fk_client;
    }

    public function setFkClient(?Clients $fk_client): static
    {
        $this->fk_client = $fk_client;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): static
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(?\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(?\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getDureeSem(): ?int
    {
        return $this->duree_sem;
    }

    public function setDureeSem(int $duree_sem): static
    {
        $this->duree_sem = $duree_sem;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function isFactureEmise(): ?bool
    {
        return $this->facture_emise;
    }

    public function setFactureEmise(bool $facture_emise): static
    {
        $this->facture_emise = $facture_emise;

        return $this;
    }

    public function isPaiementRecu(): ?bool
    {
        return $this->paiement_recu;
    }

    public function setPaiementRecu(bool $paiement_recu): static
    {
        $this->paiement_recu = $paiement_recu;

        return $this;
    }

    public function getRetourClient(): ?string
    {
        return $this->retour_client;
    }

    public function setRetourClient(?string $retour_client): static
    {
        $this->retour_client = $retour_client;

        return $this;
    }

    public function getRetourEquipe(): ?string
    {
        return $this->retour_equipe;
    }

    public function setRetourEquipe(?string $retour_equipe): static
    {
        $this->retour_equipe = $retour_equipe;

        return $this;
    }

    public function isClos(): ?bool
    {
        return $this->clos;
    }

    public function setClos(bool $clos): static
    {
        $this->clos = $clos;

        return $this;
    }

    /**
     * @return Collection<int, EquipChantier>
     */
    public function getEquipes(): Collection
    {
        return $this->equipes;
    }

    public function addEquipe(EquipChantier $equipe): static
    {
        if (!$this->equipes->contains($equipe)) {
            $this->equipes->add($equipe);
            $equipe->setFkChantier($this);
        }

        return $this;
    }

    public function removeEquipe(EquipChantier $equipe): static
    {
        if ($this->equipes->removeElement($equipe)) {
            // set the owning side to null (unless already changed)
            if ($equipe->getFkChantier() === $this) {
                $equipe->setFkChantier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Photos>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photos $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setFkChantier($this);
        }

        return $this;
    }

    public function removePhoto(Photos $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getFkChantier() === $this) {
                $photo->setFkChantier(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nom;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }
}
