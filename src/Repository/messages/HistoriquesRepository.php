<?php

namespace App\Repository\messages;


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
    public function getNbCourrierByUser(Utilisateurs $utilisateurs, bool $isSend): int{
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.utilisateur = :utilisateur')
            ->andWhere('c.isSend = :isSend')
            ->andWhere('c.deletedAt is null' )
            ->setParameter('utilisateur', $utilisateurs)
            ->setParameter('isSend', $isSend)
            ->getQuery()
            ->getSingleScalarResult();
    }


}
