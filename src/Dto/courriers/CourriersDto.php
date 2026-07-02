<?php

namespace App\Dto\courriers;

use Symfony\Component\Validator\Constraints as Assert;

class CourriersDto
{
    #[Assert\NotBlank(message: "L'objet est obligatoire.")]
    private ?string $object = null;

    private ?string $description = null;

    private ?bool $isConfidentiel = false;

    /**
     * @var DetailPersonnesDto[]
     */
    #[Assert\Valid]
    private array $detailPersonnes = [];

    // ===== GETTERS =====

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getIsConfidentiel(): ?bool
    {
        return $this->isConfidentiel;
    }

    /**
     * @return DetailPersonnesDto[]
     */
    public function getDetailPersonnes(): array
    {
        return $this->detailPersonnes;
    }

    // ===== SETTERS =====

    public function setObject(?string $object): self
    {
        $this->object = $object;
        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setIsConfidentiel(?bool $isConfidentiel): self
    {
        $this->isConfidentiel = $isConfidentiel;
        return $this;
    }

    /**
     * @param DetailPersonnesDto[] $detailPersonnes
     */
    public function setDetailPersonnes(array $detailPersonnes): self
    {
        $this->detailPersonnes = $detailPersonnes;
        return $this;
    }

    public function addDetailPersonne(DetailPersonnesDto $detailPersonne): self
    {
        $this->detailPersonnes[] = $detailPersonne;
        return $this;
    }
}