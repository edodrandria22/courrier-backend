<?php

namespace App\Repository\utilisateurs;

use App\Entity\utilisateurs\VueUtilisateurs;

use App\Repository\utils\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class VueUtilisateursRepository extends BaseRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VueUtilisateurs::class);
    }
    
}
