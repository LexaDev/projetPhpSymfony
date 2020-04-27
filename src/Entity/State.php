<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StateRepository")
 */
class State
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Choice({"Créée", "Ouverte", "Clôturée", "Activité en cours", "Passée", "Annulé"})
     * @ORM\Column(type="string")
     */
    private $label;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Outing", mappedBy="state")
     */
    private $outings;

    public function __construct()
    {
        $this->outings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label): void
    {
        $this->label = $label;
    }

    /**
     * @return Collection
     */
    public function getOutings(): Collection
    {
        return $this->outings;
    }

    /**
     * @param ArrayCollection $outings
     */
    public function setOutings(ArrayCollection $outings): void
    {
        $this->outings = $outings;
    }

}
