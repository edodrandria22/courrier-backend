<?php

namespace App\Repository\messages;

use App\Dto\utils\OrderCriteria;
use App\Entity\courriers\Courriers;
use App\Entity\messages\Historiques;
use App\Entity\utilisateurs\Utilisateurs;
use App\Repository\utils\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class HistoriquesRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Historiques::class);
    }


}
