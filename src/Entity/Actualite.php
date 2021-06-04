<?php

namespace App\Entity;
use DateTimeInterface;
use App\Repository\ActualiteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ActualiteRepository::class)
 */
class Actualite
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
    private $actu;

    /**
     * @ORM\Column(type="datetime")
     */
    private $Date_actu;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActu(): ?string
    {
        return $this->actu;
    }

    public function setActu(string $actu): self
    {
        $this->actu = $actu;

        return $this;
    }

    public function getDateActu(): ?DateTimeInterface
    {
        return $this->Date_actu;
    }

    public function setDateActu(DateTimeInterface $Date_actu): self
    {
        $this->Date_actu = $Date_actu;

        return $this;
    }
}