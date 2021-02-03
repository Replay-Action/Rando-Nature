<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LieuRepository::class)
 */
class Lieu
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom_ville;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cp_ville;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $num_rue;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom_rue;

    /**
     * @ORM\OneToMany(targetEntity=Activite::class, mappedBy="lieu")
     */
    private $activites;

    public function __construct()
    {
        $this->activites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomVille(): ?string
    {
        return $this->nom_ville;
    }

    public function setNomVille(string $nom_ville): self
    {
        $this->nom_ville = $nom_ville;

        return $this;
    }

    public function getCpVille(): ?string
    {
        return $this->cp_ville;
    }

    public function setCpVille(string $cp_ville): self
    {
        $this->cp_ville = $cp_ville;

        return $this;
    }

    public function getNumRue(): ?string
    {
        return $this->num_rue;
    }

    public function setNumRue(?string $num_rue): self
    {
        $this->num_rue = $num_rue;

        return $this;
    }

    public function getNomRue(): ?string
    {
        return $this->nom_rue;
    }

    public function setNomRue(string $nom_rue): self
    {
        $this->nom_rue = $nom_rue;

        return $this;
    }

    /**
     * @return Collection|Activite[]
     */
    public function getActivites(): Collection
    {
        return $this->activites;
    }

    public function addActivite(Activite $activite): self
    {
        if (!$this->activites->contains($activite)) {
            $this->activites[] = $activite;
            $activite->setLieu($this);
        }

        return $this;
    }

    public function removeActivite(Activite $activite): self
    {
        if ($this->activites->removeElement($activite)) {
            // set the owning side to null (unless already changed)
            if ($activite->getLieu() === $this) {
                $activite->setLieu(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return $this->nom_ville;
     }
}
