<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OutingRepository")
 */
class Outing
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Le nom est obligatoire")
     * @Assert\Length(
     *     min="3",
     *     minMessage="Le nom doit faire au moins 3 caractères",
     *     max="255",
     *     maxMessage="Le nom ne doit pas dépasser 255 caractères"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Assert\NotBlank(message="La date de début de la sortie doit être rensignée")
     * @Assert\DateTime()
     * @Assert\GreaterThan("today UTC", message="La date et l'heure de début de la sortie doit être après aujourd'hui")
     * @ORM\Column(type="datetime")
     */
    private $dateTimeStart;

    /**
     * @Assert\Positive(message="La durée ne peut pas être nul ou négative")
     * @Assert\Type(type="integer",message="La durée doit être exprimé par un nombre entier")
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @Assert\NotBlank(message="La date limite d'inscription doit être rensignée")
     * @Assert\Date()
     * @Assert\LessThan("$dateTimeStart", message="La date limite d'inscription doit être avant le début de la sortie")
     * @Assert\GreaterThanOrEqual("today UTC", message="La date limite d'inscription doit être supérieure à aujourd'hui")
     * @ORM\Column(type="date")
     */
    private $dateLimitSigningUp;

    /**
     *
     * @Assert\Positive(message="Le nombre de participants ne peut pas être nul ou négatif")
     * @ORM\Column(type="integer")
     */
    private $nbSigningUpMax;

    /**
     * @Assert\NotBlank(message="Le champ ne peut pas être vide")
     * @Assert\Length(
     *     min="3",
     *      minMessage="Les informations sur la sorties doivent faire au minimum 3 caractères"
     * )
     * @ORM\Column(type="text")
     */
    private $infosOuting;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\State", inversedBy="outings")
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="outings")
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="outings")
     */
    private $site;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Participant",inversedBy="outingsParticipate")
     */
    private $participants;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Participant", inversedBy="outingsCreated")
     */
    private $organizer;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDateTimeStart(): ?\DateTimeInterface
    {
        return $this->dateTimeStart;
    }

    public function setDateTimeStart(\DateTimeInterface $dateTimeStart): self
    {
        $this->dateTimeStart = $dateTimeStart;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDateLimitSigningUp(): ?\DateTimeInterface
    {
        return $this->dateLimitSigningUp;
    }

    public function setDateLimitSigningUp(\DateTimeInterface $dateLimitSigningUp): self
    {
        $this->dateLimitSigningUp = $dateLimitSigningUp;

        return $this;
    }

    public function getNbSigningUpMax(): ?int
    {
        return $this->nbSigningUpMax;
    }

    public function setNbSigningUpMax(int $nbSigningUpMax): self
    {
        $this->nbSigningUpMax = $nbSigningUpMax;

        return $this;
    }

    public function getInfosOuting(): ?string
    {
        return $this->infosOuting;
    }

    public function setInfosOuting(string $infosOuting): self
    {
        $this->infosOuting = $infosOuting;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state): void
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location): void
    {
        $this->location = $location;
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
     * @return Collection
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    //Methode remplaçant setParticipants qui permet d'ajouter un participant sans écraser le reste de la collection
    public function addParticipant($participant)
    {
        $this->participants->add($participant);
    }
    //Methode remplaçant setParticipants qui permet de supprimer un seul participant sans écraser le reste de la collection
    public function removeParticipant($participant)
    {
        $this->participants->removeElement($participant);
    }

    /**
     * regarde si participant en parametre est dans la liste des participant
     * @param $participant
     * @return bool
     */
    public function isParticipant($participant)
    {
        foreach ( $this->getParticipants() as $parti)
        {
            if ($parti === $participant)
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Teste si on inscription possible sur la sortie return true si ok
     * @return bool
     */
    public function canSubscribe()
    {
        if ($this->getDateLimitSigningUp()>new DateTime('now') && count($this->getParticipants())<$this->getNbSigningUpMax() && $this->getState()->getId() == 2)
        {
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getOrganizer()
    {
        return $this->organizer;
    }

    /**
     * @param mixed $organizer
     */
    public function setOrganizer($organizer): void
    {
        $this->organizer = $organizer;
    }



}
