<?php

namespace App\Entity;

use App\Repository\PhotoAlbumRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PhotoAlbumRepository::class)
 */
class PhotoAlbum
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
    private $image;

    /**
     * @ORM\ManyToOne (targetEntity=Activite::class, inversedBy="albumPhoto")
     */
    private $activite;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): void
    {
        $this->image = $image;
    }

    public function getActivite()
    {
        return $this->activite;
    }

    public function setActivite($activite): void
    {
        $this->activite = $activite;
    }


}
