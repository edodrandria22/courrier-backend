<?php

namespace App\Entity\courriers;

use App\Repository\courriers\DetailPersonnesRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\courriers\Courriers;
#[ORM\Entity(repositoryClass: DetailPersonnesRepository::class)]    
class DetailPersonnes extends BaseDetailsPersonnes
{   #[ORM\JoinColumn(nullable: true)]
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
   
}