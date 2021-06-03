<?php

namespace App\Entity;

use App\Repository\ActiviteRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ActiviteRepository::class)
 */
class Activite
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
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_activite;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duree;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $distance;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $infos_activite;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $denivele;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $difficulte;

    /**
     * @ORM\ManyToOne (targetEntity=Categorie::class, inversedBy="activites")
     */
    public $categorie;

    /**
     * @ORM\ManyToOne(targetEntity=Etat::class, inversedBy="activites")
     */
    private $etat;

    /**
     * @Assert\Valid()
     *
     * @ORM\ManyToOne(targetEntity=Lieu::class, inversedBy="activites",
     *     cascade={"persist", "remove"})
     */
    private $lieu;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="inscription")
     */
    private $users;

    /**
     * @ORM\OneToMany (targetEntity=PhotoAlbum::class, mappedBy="activite")
     */
    private $albumPhoto;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="activite")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $organisateur;

    /**
     * @ORM\OneToMany(targetEntity=DocPdf::class, mappedBy="pdfactivite",
     *     cascade={"persist","remove"})
     */
    private $docPdfs;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->docPdfs = new ArrayCollection();
    }

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

    public function getDateActivite(): ?DateTimeInterface
    {
        return $this->date_activite;
    }

    public function setDateActivite(DateTimeInterface $date_activite): self
    {
        $this->date_activite = $date_activite;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(?int $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getInfosActivite(): ?string
    {
        return $this->infos_activite;
    }

    public function setInfosActivite(?string $infos_activite): self
    {
        $this->infos_activite = $infos_activite;

        return $this;
    }

    public function getDenivele(): ?int
    {
        return $this->denivele;
    }

    public function setDenivele(?int $denivele): self
    {
        $this->denivele = $denivele;

        return $this;
    }

    public function getDifficulte(): ?int
    {
        return $this->difficulte;
    }

    public function setDifficulte(?int $difficulte): self
    {
        $this->difficulte = $difficulte;

        return $this;
    }

    public function getCategorie()
    {
        return $this->categorie;
    }

    public function setCategorie($categorie): void
    {
        $this->categorie = $categorie;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getAlbumPhoto()
    {
        return $this->albumPhoto;
    }

    public function setAlbumPhoto($albumPhoto): void
    {
        $this->albumPhoto = $albumPhoto;
    }


    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addInscription($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeInscription($this);
        }

        return $this;
    }

    public function getOrganisateur(): ?User
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?User $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    /**
     * @return Collection|DocPdf[]
     */
    public function getDocPdfs(): Collection
    {
        return $this->docPdfs;
    }

    public function addDocPdf(DocPdf $docPdf): self
    {
        if (!$this->docPdfs->contains($docPdf)) {
            $this->docPdfs[] = $docPdf;
            $docPdf->setPdfactivite($this);
        }

        return $this;
    }

    public function removeDocPdf(DocPdf $docPdf): self
    {
        if ($this->docPdfs->removeElement($docPdf)) {
            // set the owning side to null (unless already changed)
            if ($docPdf->getPdfactivite() === $this) {
                $docPdf->setPdfactivite(null);
            }
        }

        return $this;
    }


}
