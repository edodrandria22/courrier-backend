<?php

namespace App\Entity\utils;

use App\Entity\courriers\CourrierValidations;
use App\Repository\utils\FichiersRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;


#[ORM\Entity(repositoryClass: FichiersRepository::class)]
class FichiersValidations extends BaseEntite
{
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $binaire = null;

    #[ORM\ManyToOne(targetEntity: CourrierValidations::class, inversedBy: 'fichiers')]
    #[ORM\JoinColumn(nullable: true, onDelete: "CASCADE")]
    private ?CourrierValidations $courrierValidation = null;

    #[ORM\Column(length: 255)]
    private ?string $typeFichier = null;

    // ----- Getters & Setters -----

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getBinaire()
    {
        return $this->binaire;
    }

    public function setBinaire($binaire): static
    {
        $this->binaire = $binaire;
        return $this;
    }

    public function getCourrierValidation(): ?CourrierValidations
    {
        return $this->courrierValidation;
    }

    public function setCourrierValidation(?CourrierValidations $courrierValidation): static
    {
        $this->courrierValidation = $courrierValidation;
        return $this;
    }

    public function getTypeFichier(): ?string
    {
        return $this->typeFichier;
    }

    public function setTypeFichier(string $typeFichier): static
    {
        $this->typeFichier = $typeFichier;
        return $this;
    }

    public function toArray(array $exclude = []): array
    {
        // Le binaire n'est jamais inclus dans le JSON — utiliser /fichiers/{id}/download
        return parent::toArray(array_merge($exclude, ['binaire', 'courrierValidation']));
    }
}
