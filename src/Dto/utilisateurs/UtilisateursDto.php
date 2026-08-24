<?php

namespace App\Dto\utilisateurs;

use Symfony\Component\Validator\Constraints as Assert;

class UtilisateursDto
{
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "Format d'email invalide.")]
    public ?string $email = null;

    // #[Assert\NotBlank(message: "Le mot de passe est obligatoire.")]
    #[Assert\Length(
        min: 6,
        minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères."
    )]
    public ?string $mdp = null;

    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    public ?string $nom = null;

    // #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    public ?string $prenom = null;

    #[Assert\Length(max: 255)]
    public ?string $adresse = null;

    #[Assert\NotNull(message: "Le rôle est obligatoire.")]
    #[Assert\Type(
        type: 'integer',
        message: "L'identifiant du rôle doit être un entier."
    )]
    #[Assert\Positive(message: "L'identifiant du rôle doit être positif.")]
    public ?int $idRole = null;

    // ===== GETTERS =====

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function getIdRole(): ?int
    {
        return $this->idRole;
    }

}