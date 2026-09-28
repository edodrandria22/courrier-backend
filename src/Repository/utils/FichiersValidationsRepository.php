<?php

namespace App\Repository\utils;

use App\Entity\utils\FichiersValidations;
use Doctrine\Persistence\ManagerRegistry;

class FichiersValidationsRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FichiersValidations::class);
    }

    // Exemples de méthodes personnalisées

    /**
     * Récupère tous les fichiers actifs (non supprimés)
     */
    
}
