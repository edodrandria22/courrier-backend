<?php

namespace App\Repository\courriers;

use App\Entity\courriers\VueHistoriqueDetails;

use App\Repository\utils\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class VueHistoriqueDetailsRepository extends BaseRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VueHistoriqueDetails::class);
    }
    
}
