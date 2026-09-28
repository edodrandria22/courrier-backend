<?php

namespace App\Entity\courriers;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\utilisateurs\Utilisateurs;
use App\Entity\utils\BaseEntite;
use App\Entity\utils\FichiersValidations;
use App\Repository\courriers\CourrierValidationsRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

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
    
    #[ORM\Column(type: "integer", nullable: true)]
    protected ?int $numeroDepart = null;
    #[ORM\OneToMany(mappedBy: 'courrier', targetEntity: DetailPersonnesValidations::class, cascade: ['persist', 'remove'])]
    private Collection $detailPersonnes;
    
    #[ORM\OneToMany(mappedBy: 'courrierValidation', targetEntity: FichiersValidations::class, cascade: ['persist', 'remove'])]
    private Collection $fichiersValidations;

    public function __construct()
    {
        $this->detailPersonnes = new ArrayCollection();
        $this->fichiersValidations = new ArrayCollection();
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
    public function getNumeroDepart(): ?int
    {
        return $this->numeroDepart;
    }

    public function setNumeroDepart(?int $numeroDepart): self
    {
        $this->numeroDepart = $numeroDepart;

        return $this;
    }
    public function addDetailPersonne(DetailPersonnesValidations $detailPersonne): self
    {
        if (!$this->detailPersonnes->contains($detailPersonne)) {
            $this->detailPersonnes->add($detailPersonne);
            $detailPersonne->setCourrierValidation($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, FichiersValidations>
     */
    public function getFichiersvalidations(): Collection
    {
        return $this->fichiersValidations;
    }

    public function addFichier(FichiersValidations $fichier): self
    {
        if (!$this->fichiersValidations->contains($fichier)) {
            $this->fichiersValidations->add($fichier);
            $fichier->setCourrierValidation($this);
        }

        return $this;
    }

    public function removeFichier(FichiersValidations $fichier): self
    {
        if ($this->fichiersValidations->removeElement($fichier)) {
            // set the owning side to null (unless already changed)
            if ($fichier->getCourrierValidation() === $this) {
                $fichier->setCourrierValidation(null);
            }
        }

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
