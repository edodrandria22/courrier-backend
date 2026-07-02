<?php

namespace App\Repository\courriers;

use App\Entity\courriers\DetailPersonnes;

use App\Repository\utils\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class DetailPersonnesRepository extends BaseRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailPersonnes::class);
    }
    
}
