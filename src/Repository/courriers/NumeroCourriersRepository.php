<?php

namespace App\Repository\courriers;

use App\Entity\courriers\NumeroCourriers;

use App\Repository\utils\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class NumeroCourriersRepository extends BaseRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NumeroCourriers::class);
    }
    
}
