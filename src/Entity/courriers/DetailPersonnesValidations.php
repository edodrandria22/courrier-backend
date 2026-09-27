<?php

namespace App\Entity\courriers;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\courriers\DetailPersonnesValidationsRepository;

#[ORM\Entity(repositoryClass: DetailPersonnesValidationsRepository::class)]    
class DetailPersonnesValidations extends BaseDetailsPersonnes
{   #[ORM\JoinColumn(nullable: true)]
    protected ?CourrierValidations $courrierValidation = null;
    public function getCourrierValidation(): ?CourrierValidations
    {
        return $this->courrierValidation;
    }
    public function setCourrierValidation(?CourrierValidations $courrierValidation): self
    {
        $this->courrierValidation = $courrierValidation;
        return $this;
    }
   
}