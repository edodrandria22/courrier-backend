<?php

namespace App\Repository\utils;

use App\Dto\utils\OrderCriteria;
use App\Dto\utils\PaginationCriteria;
use App\Dto\utils\ConditionCriteria;
use App\Dto\utils\JoinCriteria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

abstract class BaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    public function getById(int $id): ?object
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.id = :id')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getAll(OrderCriteria $orderCriteria): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.deletedAt IS NULL')
            ->orderBy('b.' . $orderCriteria->getField(), $orderCriteria->getDirection())
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche multicritère avec pagination, order et joins
     *
     * @param ConditionCriteria[] $conditions
     * @param OrderCriteria|null $order
     * @param PaginationCriteria|null $pagination
     * @param JoinCriteria[] $joins
     * @return array
     */
    public function search(
        array $conditions = [],
        ?OrderCriteria $order = null,
        ?PaginationCriteria $pagination = null,
        array $joins = []
    ): array {
        $qb = $this->createQueryBuilder('m');

        $this->applyJoins($qb, $joins);
        $this->applyConditions($qb, $conditions);
        $this->applyDeletedAtFilter($qb);
        $this->applyOrder($qb, $order);
        $this->applyPagination($qb, $pagination);

        return $qb->getQuery()->getResult();
    }

    private function applyJoins($qb, array $joins): void
    {
        
        foreach ($joins as $join) {
            match ($join->getType()) {
                'LEFT' => $qb->leftJoin($join->getRelation(), $join->getAlias()),
                'INNER' => $qb->innerJoin($join->getRelation(), $join->getAlias()),
                default => throw new \InvalidArgumentException("Join type {$join->getType()} non supporté")
            };
            $qb->addSelect($join->getAlias());
        }
    }

    private function applyConditions($qb, array $conditions): void
    {
        foreach ($conditions as $i => $cond) {

            $param = 'param_' . $i;
            $field = $cond->getField();
            $operator = strtoupper($cond->getOperator());
            $value = $cond->getValue();

            if (!str_contains($field, '.')) {
                $field = 'm.' . $field;
            }

            // Convertir entité -> id
            if (is_object($value) && method_exists($value, 'getId')) {
                $value = $value->getId();
                $field .= '.id';
            }

            $expr = match ($operator) {

                '=' => $qb->expr()->eq($field, ':' . $param),

                'LIKE' => $qb->expr()->like($field, ':' . $param),

                '>' => $qb->expr()->gt($field, ':' . $param),

                '>=' => $qb->expr()->gte($field, ':' . $param),

                '<' => $qb->expr()->lt($field, ':' . $param),

                '<=' => $qb->expr()->lte($field, ':' . $param),

                'IN' => $qb->expr()->in($field, ':' . $param),

                'BETWEEN' => $qb->expr()->between(
                    $field,
                    ':' . $param . '_1',
                    ':' . $param . '_2'
                ),

                'IS NULL' => $qb->expr()->isNull($field),

                'IS NOT NULL' => $qb->expr()->isNotNull($field),

                default => throw new \InvalidArgumentException(
                    "Opérateur $operator non supporté"
                )
            };

            if ($operator === 'BETWEEN') {

                $qb->setParameter($param . '_1', $value[0]);
                $qb->setParameter($param . '_2', $value[1]);

            } elseif (!in_array($operator, ['IS NULL', 'IS NOT NULL'])) {

                if ($operator === 'LIKE') {
                    $value = "%$value%";
                }

                $qb->setParameter($param, $value);
            }

            $qb->andWhere($expr);
        }
    }

    private function applyDeletedAtFilter($qb): void
    {
        $qb->andWhere('m.deletedAt IS NULL');
    }

    private function applyOrder($qb, ?OrderCriteria $order): void
    {
        if ($order) {
            $qb->orderBy('m.' . $order->getField(), $order->getDirection());
        }
    }

    private function applyPagination($qb, ?PaginationCriteria $pagination): void
    {
        if ($pagination) {
            $qb->setMaxResults($pagination->getLimit());
        }
    }
}