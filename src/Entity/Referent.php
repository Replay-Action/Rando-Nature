<?php

namespace App\Entity;

use App\Repository\ReferentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReferentRepository::class)
 */
class Referent
{
    public function __toString()
    {
        return $this->getNom();
    }

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=75)
     */
    private $nom;

    /**
     * @ORM\OneToMany (targetEntity=User::class, mappedBy="referents", cascade={"persist","remove"})
     */
    private $user;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }


    /**
     * @return Collection|User
     */
    public function getUser():Collection
    {
        return $this->user;
    }

    public function setUser($user): void
    {
        $this->user = $user;
    }
}


