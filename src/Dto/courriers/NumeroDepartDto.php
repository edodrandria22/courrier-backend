<?php

namespace App\Dto\courriers;

use Symfony\Component\Validator\Constraints as Assert;

class NumeroDepartDto
{
    #[Assert\NotBlank(message: "Le numéro de départ est obligatoire.")]
    private ?int $numero = null;    
    #[Assert\NotNull(message: "Le type de départ isSend est obligatoire.")]
    private ?bool $isSend = false;

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function getIsSend(): ?bool
    {
        return $this->isSend;
    }
    public function setNumero(?int $numero): self
    {
        $this->numero = $numero;
        return $this;
    }

    public function setIsSend(?bool $isSend): self
    {
        $this->isSend = $isSend;
        return $this;
    }

}