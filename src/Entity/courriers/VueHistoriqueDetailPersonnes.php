<?php

namespace App\Entity\courriers;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\courriers\VueHistoriqueDetailPersonnesRepository;
use App\Entity\courriers\BaseVueHistoriqueDetails;

#[ORM\Entity(repositoryClass: VueHistoriqueDetailPersonnesRepository::class, readOnly: true)]
#[ORM\Table(name: "vue_historique_detail_personnes")]
#[ORM\MappedSuperclass]
class VueHistoriqueDetailPersonnes extends BaseVueHistoriqueDetails
{
    #[ORM\Column(type: "string", length: 255, nullable: true)]
    protected ?string $name = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    protected ?string $email = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    protected ?string $prenom = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    protected ?string $telephone = null;
    public function __construct()
    {
    }
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
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

}