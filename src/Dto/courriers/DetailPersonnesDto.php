<?php

namespace App\Dto\courriers;

use Symfony\Component\Validator\Constraints as Assert;

class DetailPersonnesDto
{

    private ?string $name = null;

    private ?string $prenom = null;

    #[Assert\Email(message: "L'adresse email n'est pas valide.")]
    // #[Assert\NotBlank(message: "L'adresse email est obligatoire.")]
    private ?string $email = null;

    private ?string $telephone = null;

    private ?int $matricule = null;

    private ?string $employeur = null;

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
    
    public function getMatricule(): ?int
    {
        return $this->matricule;
    }
    
    public function setMatricule(?int $matricule): self
    {
        $this->matricule = $matricule;
        return $this;
    }
    
    public function getEmployeur(): ?string
    {
        return $this->employeur;
    }
    
    public function setEmployeur(?string $employeur): self
    {
        $this->employeur = $employeur;
        return $this;
    }
}