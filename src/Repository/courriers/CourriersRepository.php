<?php

namespace App\Repository\courriers;

use App\Entity\courriers\Courriers;

use App\Repository\utils\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class CourriersRepository extends BaseRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Courriers::class);
    }
    public function countDailyCourriers(\DateTimeInterface $date): int
    {
        $start = \DateTimeImmutable::createFromInterface($date)->setTime(0, 0, 0);
        $end = $start->modify('+1 day');

        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.createdAt >= :start')
            ->andWhere('c.createdAt < :end')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function countYearlyCourriers(int $annee): int
    {
        $start = new \DateTimeImmutable($annee . '-01-01 00:00:00');
        $end = $start->modify('+1 year');

        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.createdAt >= :start')
            ->andWhere('c.createdAt < :end')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function findByReference(string $reference): ?Courriers
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.reference = :ref')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('ref', $reference)
            ->getQuery()
            ->getOneOrNullResult();
    }
    /**
     * Recherche de courriers par nom et/ou prénom (insensible à la casse)
     * @return Courriers[]
     */
    
    public function getAllCourierDisponible(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.deletedAt IS NULL')
            ->andWhere('c.dateMessage IS NULL')
            ->andWhere('c.dateValidation IS NULL')
            ->getQuery()
            ->getResult();
    }
}
