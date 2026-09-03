<?php

namespace App\Dto\courriers;

use Symfony\Component\Validator\Constraints as Assert;

class CourriersDto
{
    #[Assert\NotBlank(message: "L'objet est obligatoire.")]
    private ?string $object = null;

    private ?string $description = null;

    private ?bool $isConfidentiel = false;
    private ?string $observation = null;

    // #[Assert\NotBlank(message: "Le numero d' arrivée est obligatoire.")]
    #[Assert\Positive(message: "Le numero d' arrivée doit être positif.")]
    #[Assert\Type('integer', message: "Le numero d' arrivée doit être un entier.")]
    
    private ?int $numeroArrive = null;


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
    public function getObservation(): ?string
    {
        return $this->observation;
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
    public function setObservation(?string $observation): self
    {
        $this->observation = $observation;
        return $this;
    }

    public function setIsConfidentiel(?bool $isConfidentiel): self
    {
        $this->isConfidentiel = $isConfidentiel;
        return $this;
    }
    public function setNumeroArrive(?int $numeroArrive): self 
    {
        $this->numeroArrive = $numeroArrive;
        return $this;
    }
    public function getNumeroarrive(): ?int
    {
        return $this->numeroArrive;
    }

    /**
     * @param DetailPersonnesDto[] $detailPersonnes
     */
    public function setDetailPersonnes(array $detailPersonnes): self
    {
        $this->detailPersonnes = array_map(function ($item) {
            // Si c'est déjà une instance du DTO, on la garde
            if ($item instanceof DetailPersonnesDto) {
                return $item;
            }

            // Si c'est un tableau PHP, on instancie le DTO
            if (is_array($item)) {
                $dto = new DetailPersonnesDto();
                if (isset($item['name'])) $dto->setName($item['name']);
                if (isset($item['prenom'])) $dto->setPrenom($item['prenom']);
                if (isset($item['email'])) $dto->setEmail($item['email']);
                if (isset($item['telephone'])) $dto->setTelephone($item['telephone']);
                
                return $dto;
            }

            return $item;
        }, $detailPersonnes);

        return $this;
    }

    public function addDetailPersonne(DetailPersonnesDto $detailPersonne): self
    {
        $this->detailPersonnes[] = $detailPersonne;
        return $this;
    }
}