<?php

namespace App\Repository\courriers;

use App\Entity\courriers\CourrierValidations;

use App\Repository\utils\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class CourrierValidationsRepository extends BaseRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourrierValidations::class);
    }
    
}
