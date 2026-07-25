<?php

namespace App\Entity\utilisateurs;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\utils\BaseEntite;

#[ORM\MappedSuperclass]
abstract class BaseUtilisateurs extends BaseEntite
{
    #[ORM\Column(length: 255)]
    protected ?string $email = null;

    #[ORM\Column(length: 255)]
    protected ?string $mdp = null;

    #[ORM\Column(length: 255)]
    protected ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $adresse = null;

    #[ORM\ManyToOne(targetEntity: Roles::class)]
    #[ORM\JoinColumn(nullable: false)]
    protected ?Roles $role = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): static
    {
        $this->mdp = $mdp;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getRole(): ?Roles
    {
        return $this->role;
    }

    public function setRole(?Roles $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function toArray(array $exclude = []): array
    {
        $data = parent::toArray($exclude);

        if (!in_array('role', $exclude, true)) {
            $data['role'] = $this->getRole() ? $this->getRole()->getName() : null;
        }

        if (!in_array('idRole', $exclude, true)) {
            $data['idRole'] = $this->getRole() ? $this->getRole()->getId() : null;
        }
        $data['nom'] = $this->getNom();
        $data['prenom'] = $this->getPrenom();

        return $data;
    }
}
