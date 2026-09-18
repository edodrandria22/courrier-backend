<?php

namespace App\Repository\courriers;

use App\Entity\courriers\Entites;

use App\Repository\utils\BaseRepository;

use Doctrine\Persistence\ManagerRegistry;
class EntitesRepository extends BaseRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entites::class);
    }
    
}
