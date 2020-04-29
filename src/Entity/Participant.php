<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantRepository")
 */
class Participant implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Le champs 'Pseudo' est obligatoire.")
     * @Assert\Length(  min="5",
     *                  minMessage="Le champs 'Pseudo' doit contenir au minimum 5 caractères.",
     *                  max="30",
     *                  maxMessage="Le champs 'Pseudo' doit contenir au maximum 30 caractères.")
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @Assert\Regex(pattern="#^[\w-?!]{8,}$#", message="Le champs 'Mot de passe' doit contenir au moins 8 caractères comprenant: chiffre, lettre, _, ?, -, !.")
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Assert\NotBlank(message="Le champs 'Prénom' est obligatoire.")
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @Assert\NotBlank(message="Le champs 'Nom' est obligatoire.")
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @Assert\Regex(pattern="#^(\d{2}\s*){5}$#", message="Veuillez saisir un numéro au format 02 23 23 23 23.")
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $phoneNumber;

    /**
     * @Assert\NotBlank(message="Le champs 'Email' est obligatoire.")
     * @Assert\Regex(pattern="#^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$#", message="Veuillez saisir une adresse email valide.")
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="participants")
     */
    private $site;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Outing", mappedBy="organizer")
     */
    private $outingsCreated;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Outing",mappedBy="participants")
     */
    private $outingsParticipate;


    public function __construct()
    {
        $this->outingsParticipate = new ArrayCollection();

        $this->outingsCreated = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * @param mixed $actif
     */
    public function setActif($actif): void
    {
        $this->actif = $actif;
    }

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $site
     */
    public function setSite($site): void
    {
        $this->site = $site;
    }

    /**
     * @return ArrayCollection
     */
    public function getOutingsCreated(): ArrayCollection
    {
        return $this->outingsCreated;
    }

    /**
     * @param ArrayCollection $outingsCreated
     */
    public function setOutingsCreated(ArrayCollection $outingsCreated): void
    {
        $this->outingsCreated = $outingsCreated;
    }

    /**
     * @return ArrayCollection
     */
    public function getOutingsParticipate(): ArrayCollection
    {
        return $this->outingsParticipate;
    }

    /**
     * @param ArrayCollection $outingsParticipate
     */
    public function setOutingsParticipate(ArrayCollection $outingsParticipate): void
    {
        $this->outingsParticipate = $outingsParticipate;
    }


}
