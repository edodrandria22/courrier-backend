<?php

namespace App\Entity\courriers;


use Doctrine\ORM\Mapping as ORM;
use App\Entity\utilisateurs\Utilisateurs;
use App\Entity\utils\BaseSansId;

#[ORM\MappedSuperclass]
abstract class BaseCourriers extends BaseSansId
{
    #[ORM\Column(type: "string", length: 100, nullable: false)]
    protected ?string $reference = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    protected ?string $object = null;

    #[ORM\Column(type: "text", nullable: true)]
    protected ?string $description = null;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    protected ?\DateTimeImmutable $dateMessage = null;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected ?Utilisateurs $createur = null;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected ?Utilisateurs $cloturePar = null;

    #[ORM\Column(type: "boolean", nullable: true)]
    protected ?bool $isConfidentiel = false;
    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    protected ?\DateTimeImmutable $dateValidation = null;

    public function getDateValidation(): ?\DateTimeInterface
    {
        return $this->dateValidation;
    }

    public function setDateValidation(?\DateTimeInterface $dateValidation): self
    {
        $this->dateValidation = $dateValidation;
        return $this;
    }

    public function validate(): void
    {
        $this->dateValidation = new \DateTimeImmutable();
    }

    public function __construct()
    {
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;
        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDateMessage(): ?\DateTimeImmutable
    {
        return $this->dateMessage;
    }

    public function setDateMessage(?\DateTimeImmutable $dateMessage): self
    {
        $this->dateMessage = $dateMessage;
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

    public function getCloturePar(): ?Utilisateurs
    {
        return $this->cloturePar;
    }

    public function setCloturePar(?Utilisateurs $cloturePar): self
    {
        $this->cloturePar = $cloturePar;
        return $this;
    }

    
    public function setIsConfidentiel(?bool $isConfidentiel): self
    {
        $this->isConfidentiel = $isConfidentiel;
        return $this;
    }
    public function getIsConfidentiel(): ?bool
    {
        return $this->isConfidentiel;
    }

    public function toArray(array $exclude = []): array
    {
        $data = parent::toArray($exclude);

        // $data['createur'] = $this->getCreateur()?->toArray($exclude);
        $excludeUtilisateur = [...$exclude, 'role', 'mdp','idRole','adresse','createdAt','id'];
        $data['cloturePar'] = $this->getCloturePar()?->toArray($excludeUtilisateur);
        $data['statut'] = $this->getDateValidation() !== null ? 'finalise' : 'en_cours';

        return $data;
    }

}