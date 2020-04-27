<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateTimeStart;

    /**
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\Column(type="date")
     */
    private $dateLimitSigningUp;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbSigningUpMax;

    /**
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
