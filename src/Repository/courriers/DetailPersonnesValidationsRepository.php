<?php

namespace App\Repository\courriers;

use App\Entity\courriers\DetailPersonnesValidations;
use App\Repository\utils\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class DetailPersonnesValidationsRepository extends BaseRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailPersonnesValidations::class);
    }
    
}
