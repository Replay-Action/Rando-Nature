<?php

namespace App\Entity;

use App\Repository\DocumentationRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DocumentationRepository::class)
 */
class Documentation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
    * @ORM\Column(type="datetime", nullable=true)
    */
    private $date_modifier;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $auteur;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le champ doit contenir du texte pour être validable")
     */
    private $titre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $categorie;

    /**
     * @ORM\Column (type="text")
     * @Assert\NotBlank(message="Le champ doit contenir du texte pour être validable")
     */
    private $intro;
    /**
     * @ORM\Column (type="text")
     * @Assert\NotBlank(message="Le champ doit contenir du texte pour être validable")
     */
    private $paragraphe1;

    /**
     * @ORM\Column (type="text", nullable=true)
     * @Assert\NotBlank(message="Le champ doit contenir du texte pour être validable")
     */
    private $paragraphe2;

    /**
     * @ORM\Column(type="string", length=255,  nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255,  nullable=true)
     */
    private $imageModification;

    /**
     * @ORM\Column(type="string", length=255,  nullable=true)
     * @Assert\NotBlank(message="Le champ doit contenir du texte pour être validable")
     */
    private $imageLegende;

    /**
     * @ORM\Column(type="string", length=255,  nullable=true)
     */
    private $image2;

    /**
     * @ORM\Column(type="string", length=255,  nullable=true)
     */
    private $imageModification2;

    /**
     * @ORM\Column(type="string", length=255,  nullable=true)
     * @Assert\NotBlank(message="Le champ doit contenir du texte pour être validable")
     */
    private $imageLegende2;

    /**
     * @ORM\Column (type="text",  nullable=true)
     */
    private $url;

    /**
     * @ORM\OneToMany ( targetEntity=Commentaire::class, mappedBy="documentation",
     *     cascade={"persist","remove"})
     */
    private $commentaire;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreation(): ?DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation($date_creation): void
    {
        $this->date_creation = $date_creation;
    }

    public function getDateModifier()
    {
        return $this->date_modifier;
    }

    public function setDateModifier($date_modifier): void
    {
        $this->date_modifier = $date_modifier;
    }

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(?string $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getIntro()
    {
        return $this->intro;
    }

    public function setIntro($intro): void
    {
        $this->intro = $intro;
    }

    public function getParagraphe1()
    {
        return $this->paragraphe1;
    }

    public function setParagraphe1($paragraphe1): void
    {
        $this->paragraphe1 = $paragraphe1;
    }

    public function getParagraphe2()
    {
        return $this->paragraphe2;
    }

    public function setParagraphe2($paragraphe2): void
    {
        $this->paragraphe2 = $paragraphe2;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): void
    {
        $this->image = $image;
    }

    public function getImageModification(): ?string
    {
        return $this->imageModification;
    }

    public function setImageModification($imageModification): void
    {
        $this->imageModification = $imageModification;
    }

    public function getImageLegende(): ?string
    {
        return $this->imageLegende;
    }

    public function setImageLegende($imageLegende): void
    {
        $this->imageLegende = $imageLegende;
    }

    public function getImage2()
    {
        return $this->image2;
    }

    public function setImage2($image2): void
    {
        $this->image2 = $image2;
    }

    public function getImageModification2()
    {
        return $this->imageModification2;
    }

    public function setImageModification2($imageModification2): void
    {
        $this->imageModification2 = $imageModification2;
    }

    public function getImageLegende2()
    {
        return $this->imageLegende2;
    }

    public function setImageLegende2($imageLegende2): void
    {
        $this->imageLegende2 = $imageLegende2;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url): void
    {
        $this->url = $url;
    }

    public function getCommentaire()
    {
        return $this->commentaire;
    }

    public function setCommentaire($commentaire): void
    {
        $this->commentaire = $commentaire;
    }


}
