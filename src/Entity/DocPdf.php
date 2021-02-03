<?php

namespace App\Entity;

use App\Repository\DocPdfRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DocPdfRepository::class)
 */
class DocPdf
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
    private $nompdf;

    /**
     * @ORM\ManyToOne(targetEntity=Activite::class, inversedBy="docPdfs")
     */
    private $pdfactivite;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNompdf(): ?string
    {
        return $this->nompdf;
    }

    public function setNompdf(string $nompdf): self
    {
        $this->nompdf = $nompdf;

        return $this;
    }

    public function getPdfactivite(): ?Activite
    {
        return $this->pdfactivite;
    }

    public function setPdfactivite(?Activite $pdfactivite): self
    {
        $this->pdfactivite = $pdfactivite;

        return $this;
    }
}
