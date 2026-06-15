<?php

namespace App\Entity\utilisateurs;

use App\Entity\utilisateurs\BaseUtilisateurs;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\utilisateurs\VueUtilisateursRepository;

#[ORM\Entity(repositoryClass: VueUtilisateursRepository::class, readOnly: true)]
#[ORM\Table(name: "vue_utilisateurs")]
#[ORM\MappedSuperclass]
class VueUtilisateurs extends BaseUtilisateurs
{
    

    #[ORM\Column()]
    private ?string $nomComplet = null;
    
    public function getNomComplet(): ?string
    {
        return $this->nomComplet;
    }
    
    public function setNomComplet(?string $nomComplet): static
    {
        $this->nomComplet = $nomComplet;
        return $this;
    }
}
