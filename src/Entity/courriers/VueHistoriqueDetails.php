<?php

namespace App\Entity\courriers;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\courriers\VueHistoriqueDetailsRepository;
use App\Entity\courriers\BaseVueHistoriqueDetails;

#[ORM\Entity(repositoryClass: VueHistoriqueDetailsRepository::class, readOnly: true)]
#[ORM\Table(name: "vue_historique_details")]
#[ORM\MappedSuperclass]
class VueHistoriqueDetails extends BaseVueHistoriqueDetails
{

}
