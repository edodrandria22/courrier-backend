<?php

namespace App\Entity\messages;
use App\Entity\courriers\Courriers;
use App\Entity\utilisateurs\Utilisateurs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use App\Entity\utils\BaseEntite;
use App\Repository\messages\HistoriquesRepository;

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

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $numero = null;
    
    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $numRef = null;

    
    #[ORM\ManyToOne(targetEntity: Messages::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Messages $message = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observation = null;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    protected ?\DateTimeImmutable $dateReception = null;
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
    
    public function getMessage(): ?Messages
    {
        return $this->message;
    }
    
    public function setMessage(?Messages $message): static
    {
        $this->message = $message;
        return $this;
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
    public function getNumRef(): ?int
    {
        return $this->numRef;
    }
    public function setNumRef(?int $numRef): self
    {
        $this->numRef = $numRef;
        return $this;
    }
    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): static
    {
        $this->observation = $observation;
        return $this;
    }
    public function getDateReception(): ?\DateTimeImmutable
    {
        return $this->dateReception;
    }
    public function setDateReception(?\DateTimeImmutable $dateReception): static
    {
        $this->dateReception = $dateReception;
        return $this;
    }
}
