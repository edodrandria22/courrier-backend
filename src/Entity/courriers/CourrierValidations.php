<?php

namespace App\Entity\courriers;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\utilisateurs\Utilisateurs;
use App\Entity\utils\BaseEntite;
use App\Repository\courriers\CourrierValidationsRepository;

#[ORM\Entity(repositoryClass: CourrierValidationsRepository::class)]
class CourrierValidations extends BaseEntite
{
    #[ORM\Column(type: "text", nullable: false)]
    protected ?string $object = null;

    #[ORM\Column(type: "text", nullable: true)]
    protected ?string $ville = null;

    #[ORM\Column(type: "datetime_immutable")]
    protected ?\DateTimeImmutable $dateDebut = null;

    #[ORM\Column(type: "datetime_immutable")]
    protected ?\DateTimeImmutable $dateFin = null;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected ?Utilisateurs $createur = null;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    protected ?\DateTimeImmutable $dateValidation = null;

    #[ORM\Column(type: "text", nullable: true)]
    protected ?string $observation = null;

    #[ORM\Column(type: "text", nullable: true)]
    protected ?string $observationSuperviseur = null;


    public function __construct()
    {
    }
    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(?string $object): self
    {
        $this->object = $object;

        return $this;
    }
    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }
    public function getDateDebut(): ?\DateTimeImmutable
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeImmutable $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }
    public function getDateFin(): ?\DateTimeImmutable
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeImmutable $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }
    public function getCreateur(): ?Utilisateurs
    {
        return $this->createur;
    }

    public function setCreateur(?Utilisateurs $createur): self
    {
        $this->createur = $createur;

        return $this;
    }    
    public function getDateValidation(): ?\DateTimeImmutable
    {
        return $this->dateValidation;
    }

    public function setDateValidation(?\DateTimeImmutable $dateValidation): self
    {
        $this->dateValidation = $dateValidation;

        return $this;
    }

    public function validate(): void
    {
        $this->dateValidation = new \DateTimeImmutable();
    }
    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): self
    {
        $this->observation = $observation;

        return $this;
    }
    public function getObservationSuperviseur(): ?string
    {
        return $this->observationSuperviseur;
    }

    public function setObservationSuperviseur(?string $observationSuperviseur): self
    {
        $this->observationSuperviseur = $observationSuperviseur;

        return $this;
    }


    public function toArray(array $exclude = []): array
    {
        $data = parent::toArray($exclude);

        $excludeUtilisateur = [
            ...$exclude,
            'role',
            'mdp',
            'idRole',
            'adresse',
            'createdAt',
            'id'
        ];

        $data['createur'] = $this->getCreateur()?->toArray($excludeUtilisateur);
        return $data;
    }
}
