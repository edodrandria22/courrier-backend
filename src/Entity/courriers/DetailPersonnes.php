<?php

namespace App\Entity\courriers;

use App\Repository\courriers\DetailPersonnesRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\courriers\Courriers;
#[ORM\Entity(repositoryClass: DetailPersonnesRepository::class)]    
class DetailPersonnes extends BaseDetailsPersonnes
{   
    #[ORM\ManyToOne(targetEntity:Employeurs::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected ?Employeurs $employeur = null;
    
    #[ORM\ManyToOne(targetEntity:Courriers::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected ?Courriers $courrier = null;
    public function getCourrier(): ?Courriers
    {
        return $this->courrier;
    }
    public function setCourrier(?Courriers $courrier): self
    {
        $this->courrier = $courrier;
        return $this;
    }
    public function getEmployeur(): ?Employeurs
    {
        return $this->employeur;
    }
    public function setEmployeur(?Employeurs $employeur): self
    {
        $this->employeur = $employeur;
        return $this;
    }
    public function toArray(array $exclude = []): array
    {
        $data = parent::toArray($exclude);
        $employeur = $this->getEmployeur();
        $data['employeurId'] = $employeur?->getId();
        $data['employeur'] = $employeur?->getName();
        return $data;
    }
   
}