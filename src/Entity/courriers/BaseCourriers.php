<?php

namespace App\Entity\courriers;

use App\Entity\utils\BaseValidation;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\utilisateurs\Utilisateurs;

#[ORM\MappedSuperclass]
abstract class BaseCourriers extends BaseValidation
{
    #[ORM\Column(type: "string", length: 100, nullable: false)]
    protected ?string $reference = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    protected ?string $object = null;

    #[ORM\Column(type: "text", nullable: true)]
    protected ?string $description = null;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    protected ?string $email = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    protected ?string $nom = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    protected ?string $prenom = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    protected ?string $telephone = null;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    protected ?\DateTimeImmutable $dateMessage = null;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected ?Utilisateurs $createur = null;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected ?Utilisateurs $cloturePar = null;

    #[ORM\Column(type: "integer", nullable: true)]
    protected ?int $numero = null;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;
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

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(?int $numero): self
    {
        $this->numero = $numero;
        return $this;
    }

    public function toArray(array $exclude = []): array
    {
        $data = parent::toArray($exclude);

        // $data['createur'] = $this->getCreateur()?->toArray($exclude);
        $data['cloturePar'] = $this->getCloturePar()?->toArray($exclude);
        $data['statut'] = $this->getDateValidation() !== null ? 'finalise' : 'en_cours';

        return $data;
    }
}