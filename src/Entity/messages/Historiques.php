<?php

namespace App\Entity\messages;
use App\Entity\courriers\Courriers;
use App\Entity\utilisateurs\Utilisateurs;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\utils\BaseEntite;

#[ORM\Entity(repositoryClass: HistoriquesRepository::class)]
class Historiques extends BaseEntite
{
    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $utilisateur = null;

    #[ORM\ManyToOne(targetEntity: Courriers::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Courriers $courrier = null;

    #[ORM\Column(type:"boolean",nullable:false)]
    private bool $isSend;

    public function getUtilisateur(): ?Utilisateurs
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateurs $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }
    public function setIsSend(bool $isSend): static
    {
        $this->isSend = $isSend;
        return $this;
    }
    public function getIsSend(): bool
    {
        return $this->isSend;
    }

    public function getCourrier(): ?Courriers
    {
        return $this->courrier;
    }

    public function setCourrier(?Courriers $courrier): static
    {
        $this->courrier = $courrier;
        return $this;
    }
}