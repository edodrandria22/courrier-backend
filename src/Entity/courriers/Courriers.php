<?php

namespace App\Entity\courriers;

use App\Entity\courriers\BaseCourriers;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\courriers\CourriersRepository;

#[ORM\Entity(repositoryClass: CourriersRepository::class)]
class Courriers extends BaseCourriers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function setId(?int $id): static
    {
        $this->id = $id;
        return $this;
    }
}
