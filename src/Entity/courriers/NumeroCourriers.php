<?php

namespace App\Entity\courriers;


use Doctrine\ORM\Mapping as ORM;
use App\Entity\utilisateurs\Utilisateurs;
use App\Entity\utils\BaseEntite;
use App\Repository\courriers\NumeroCourriersRepository;

#[ORM\Entity(repositoryClass: NumeroCourriersRepository::class)]
class NumeroCourriers extends BaseEntite
{
    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $numero = null;
    
    #[ORM\Column(type:"boolean",nullable:false)]
    private bool $isSend;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected ?Utilisateurs $utilisateur = null;



    public function __construct()
    {
    }
    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(?int $numero): self
    {
        $this->numero = $numero;
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

    public function getUtilisateur(): ?Utilisateurs
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateurs $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

}