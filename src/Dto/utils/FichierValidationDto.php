<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class FichierValidationDto
{
    #[Assert\NotBlank(message: "Le type de fichier est obligatoire.")]
    public ?string $typeFichier = null;

    #[Assert\NotNull(message: "Le fichier est obligatoire.")]
    #[Assert\File(
        maxSize: '5M',
        maxSizeMessage: 'Le fichier ne doit pas dépasser 5 Mo.'
    )]
    public ?UploadedFile $fichier = null;

    public function getTypeFichier(): ?string
    {
        return $this->typeFichier;
    }

    public function setTypeFichier(?string $typeFichier): self
    {
        $this->typeFichier = $typeFichier;

        return $this;
    }

    public function getFichier(): ?UploadedFile
    {
        return $this->fichier;
    }

    public function setFichier(?UploadedFile $fichier): self
    {
        $this->fichier = $fichier;

        return $this;
    }
     public function __construct(
        ?string $typeFichier = null,
        ?UploadedFile $fichier = null
    ) {
        $this->typeFichier = $typeFichier;
        $this->fichier = $fichier;
    }
}