<?php

namespace App\Dto\courriers;

use Symfony\Component\Validator\Constraints as Assert;

class CourriersValidationsDto
{
    #[Assert\NotBlank(message: "L'objet est obligatoire.")]
    private ?string $object = null;

    private ?\DateTimeImmutable $dateDebut = null;

    private ?\DateTimeImmutable $dateFin = null;
    private ?string $observation = null;

    // #[Assert\NotBlank(message: "Le numero de depart est obligatoire.")]
    #[Assert\Positive(message: "Le numero de depart doit être positif.")]
    #[Assert\Type('integer', message: "Le numero de depart doit être un entier.")]
    
    private ?int $numeroDepart = null;


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
    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function getDateDebut(): ?\DateTimeImmutable
    {
        return $this->dateDebut;
    }

    public function getDateFin(): ?\DateTimeImmutable
    {
        return $this->dateFin;
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
    public function setObservation(?string $observation): self
    {
        $this->observation = $observation;
        return $this;
    }

    public function setDateDebut(?\DateTime $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function setDateFin(?\DateTime $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }
    public function setNumeroDepart(?int $numeroDepart): self 
    {
        $this->numeroDepart = $numeroDepart;
        return $this;
    }
    public function getNumeroDepart(): ?int
    {
        return $this->numeroDepart;
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
                if (isset($item['matricule'])) $dto->setMatricule($item['matricule']);
                if (isset($item['employeurId'])) $dto->setEmployeurId($item['employeurId']);
                if(isset($item['entiteId'])) $dto->setEntiteId($item['entiteId']);
                
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