<?php

namespace App\Dto\courriers;

use Symfony\Component\Validator\Constraints as Assert;

class DetailPersonnesDto
{
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    private ?string $name = null;

    private ?string $prenom = null;

    #[Assert\Email(message: "L'adresse email n'est pas valide.")]
    private ?string $email = null;

    private ?string $telephone = null;

    // ===== GETTERS =====

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    // ===== SETTERS =====

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }
}