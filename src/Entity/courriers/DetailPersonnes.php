<?php

namespace App\Entity\courriers;

use App\Entity\utils\BaseEntite;
use App\Repository\courriers\DetailPersonnesRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\courriers\Courriers;
#[ORM\Entity(repositoryClass: DetailPersonnesRepository::class)]    
class DetailPersonnes extends BaseEntite
{
    #[ORM\Column(type: "string", length: 255, nullable: true)]
    protected ?string $name = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    protected ?string $email = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    protected ?string $prenom = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    protected ?string $telephone = null;
    
    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $matricule = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    protected ?string $employeur = null;
    
    #[ORM\ManyToOne(targetEntity: Courriers::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected ?Courriers $courrier = null;

    #[ORM\ManyToOne(targetEntity:Entites::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected ?Entites $entites = null;
    public function __construct()
    {
    }
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }
    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }
    public function getCourrier(): ?Courriers
    {
        return $this->courrier;
    }
    public function setCourrier(?Courriers $courrier): self
    {
        $this->courrier = $courrier;
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
    public function getEntites(): ?Entites
    {
        return $this->entites;
    }
    public function setEntites(?Entites $entites): self
    {
        $this->entites = $entites;
        return $this;
    }
    
}