<?php

namespace App\Entity;

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
}
