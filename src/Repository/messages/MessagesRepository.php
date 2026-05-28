<?php

namespace App\Repository\messages;

use App\Entity\messages\Messages;
use App\Repository\utils\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class MessagesRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Messages::class);
    }

    public function isUserInvolvedInCourrier(int $courrierId, int $userId): bool
    {
        $count = $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->andWhere('m.courrier = :courrierId')
            ->andWhere('m.expediteur = :userId OR m.destinataire = :userId')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameter('courrierId', $courrierId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
    public function getById(int $id): ?object
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.expediteur', 'e')
            ->leftJoin('m.destinataire', 'd')
            ->addSelect('e', 'd')
            ->andWhere('m.id = :id')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
