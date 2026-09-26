<?php

namespace App\Repository\courriers;

use App\Entity\courriers\Employeurs;

use App\Repository\utils\BaseRepository;

use Doctrine\Persistence\ManagerRegistry;
class EmployeursRepository extends BaseRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employeurs::class);
    }
    
}
