<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $login = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 36)]
    private ?string $nom = null;

    #[ORM\Column(length: 36)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column]
    private ?int $telephone = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_in = null;

    #[ORM\Column]
    private ?bool $present = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_out = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Equipes $fk_equipe = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Status $fk_status = null;

    #[ORM\OneToMany(mappedBy: 'fk_destinataire', targetEntity: Messages::class)]
    private Collection $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->login;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTelephone(): ?int
    {
        return $this->telephone;
    }

    public function setTelephone(int $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getDateIn(): ?\DateTimeInterface
    {
        return $this->date_in;
    }

    public function setDateIn(\DateTimeInterface $date_in): static
    {
        $this->date_in = $date_in;

        return $this;
    }

    public function isPresent(): ?bool
    {
        return $this->present;
    }

    public function setPresent(bool $present): static
    {
        $this->present = $present;

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

    public function getFkEquipe(): ?Equipes
    {
        return $this->fk_equipe;
    }

    public function setFkEquipe(?Equipes $fk_equipe): static
    {
        $this->fk_equipe = $fk_equipe;

        return $this;
    }

    public function getFkStatus(): ?Status
    {
        return $this->fk_status;
    }

    public function setFkStatus(?Status $fk_status): static
    {
        $this->fk_status = $fk_status;

        return $this;
    }

    /**
     * @return Collection<int, Messages>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Messages $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setFkDestinataire($this);
        }

        return $this;
    }

    public function removeMessage(Messages $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getFkDestinataire() === $this) {
                $message->setFkDestinataire(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->prenom . " " . $this->nom;
    }
}
