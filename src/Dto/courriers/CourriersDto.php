<?php

namespace App\Dto\courriers;

use Symfony\Component\Validator\Constraints as Assert;

class CourriersDto
{
    #[Assert\NotBlank(message: "Object est obligatoire.")]
    #[Assert\Length(max: 255)]
    private ?string $object = null;

    private ?string $description = null;

    // #[Assert\Email(message: "L'adresse mail n'est pas valide.")]
    private ?string $email = null;

    // #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(max: 255)]
    private ?string $nom = null;

    // #[Assert\NotBlank(message: "Le prenom est obligatoire.")]
    #[Assert\Length(max: 255)]
    private ?string $prenom = null;

    #[Assert\Length(max: 255)]
    private ?string $telephone = null;

    // ===== GETTERS =====

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    // ===== SETTERS =====

    public function setObject(?string $object): self
    {
        $this->object = $object;
        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }
}