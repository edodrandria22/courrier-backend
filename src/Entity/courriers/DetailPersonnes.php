<?php

namespace App\Entity\courriers;

use App\Repository\courriers\DetailPersonnesRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\utils\BaseNom;
#[ORM\Entity(repositoryClass: DetailPersonnesRepository::class)]    
class DetailPersonnes extends BaseNom
{
    #[ORM\Column(type: "string", length: 255, nullable: true)]
    protected ?string $email = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    protected ?string $prenom = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    protected ?string $telephone = null;
    
    #[ORM\ManyToOne(targetEntity: Courriers::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected ?Courriers $courrier = null;
    public function __construct()
    {
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
}