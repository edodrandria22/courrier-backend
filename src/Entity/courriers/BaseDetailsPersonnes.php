<?php

namespace App\Entity\courriers;

use App\Entity\utils\BaseEntite;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass] 
abstract class BaseDetailsPersonnes extends BaseEntite
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
    protected ?int $matricule = null;

    #[ORM\ManyToOne(targetEntity:Entites::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected ?Entites $entite = null;
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
    public function getMatricule(): ?int
    {
        return $this->matricule;
    }

    public function setMatricule(?int $matricule): self
    {
        $this->matricule = $matricule;
        return $this;
    }
    public function getEntite(): ?Entites
    {
        return $this->entite;
    }
    public function setEntite(?Entites $entite): self
    {
        $this->entite = $entite;
        return $this;
    }
     public function toArray(array $exclude = []): array
    {
        $data = parent::toArray($exclude);
        $entite = $this->getEntite();
        $data['entiteId'] = $entite?->getId();
        $data['entite'] = $entite?->getName();
        return $data;
    }
    
}