<?php

namespace App\Entity\courriers;

use App\Entity\courriers\BaseCourriers;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\courriers\VueHistoriqueDetailsRepository;

#[ORM\Entity(repositoryClass: VueHistoriqueDetailsRepository::class, readOnly: true)]
#[ORM\Table(name: "vue_historique_details")]
class VueHistoriqueDetails extends BaseCourriers
{
    #[ORM\Column(type: "integer")]
    private ?int $utilisateurId = null;
    
    
    public function getUtilisateurId(): ?int
    {
        return $this->utilisateurId;
    }
    
    public function setUtilisateurId(?int $utilisateurId): self
    {
        $this->utilisateurId = $utilisateurId;
        return $this;
    }
    
}
