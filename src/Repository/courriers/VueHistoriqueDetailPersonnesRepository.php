<?php

namespace App\Repository\courriers;

use App\Dto\courriers\RechercheCourriersDto;
use App\Dto\utils\OrderCriteria;
use App\Dto\utils\PaginationCriteria;
use App\Entity\courriers\VueHistoriqueDetailPersonnes;
use App\Entity\utilisateurs\Utilisateurs;
use App\Repository\utils\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class VueHistoriqueDetailPersonnesRepository extends BaseRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VueHistoriqueDetailPersonnes::class);
    }
    public function searchByDto(
        Utilisateurs $utilisateur,
        RechercheCourriersDto $dto,
        OrderCriteria $orderCriteria,
        PaginationCriteria $paginationCriteria
    ): array {
        $qb = $this->createQueryBuilder('m')
            ->select('DISTINCT m');

        if ($utilisateur->getRole()->getName() !== 'Admin') {
            $qb->andWhere('m.utilisateurId = :utilisateurId')
                ->setParameter('utilisateurId', $utilisateur->getId());
        } else {
            $qb->andWhere('m.isSend = :isSend')
                ->setParameter('isSend', false);
        }

        $qb->andWhere('m.createdAt < :cursor')
            ->setParameter('cursor', $paginationCriteria->getValue());

        if ($this->notEmpty($dto->reference)) {
            $qb->andWhere('m.reference LIKE :reference')
                ->setParameter('reference', '%' . $dto->reference . '%');
        }

        if ($this->notEmpty($dto->object)) {
            $qb->andWhere('m.object LIKE :object')
                ->setParameter('object', '%' . $dto->object . '%');
        }

        if ($this->notEmpty($dto->nom)) {
            $qb->andWhere('m.name LIKE :nom')
                ->setParameter('nom', '%' . mb_strtoupper($dto->nom, 'UTF-8') . '%');
        }

        if ($this->notEmpty($dto->prenom)) {
            $qb->andWhere('m.prenom LIKE :prenom')
                ->setParameter(
                    'prenom',
                    '%' . mb_convert_case($dto->prenom, MB_CASE_TITLE, 'UTF-8') . '%'
                );
        }

        if ($this->notEmpty($dto->email)) {
            $qb->andWhere('m.email LIKE :email')
                ->setParameter('email', '%' . $dto->email . '%');
        }

        if ($this->notEmpty($dto->telephone)) {
            $qb->andWhere('m.telephone LIKE :telephone')
                ->setParameter('telephone', '%' . $dto->telephone . '%');
        }

        if ($this->notEmpty($dto->numero)) {
            $qb->andWhere('m.numero = :numero')
                ->setParameter('numero', $dto->numero);
        }

        if ($dto->isConfidentiel !== null) {
            $qb->andWhere('m.isConfidentiel = :isConfidentiel')
                ->setParameter('isConfidentiel', $dto->isConfidentiel);
        }

        if ($dto->dateDebut && $dto->dateFin) {
            $qb->andWhere('m.createdAt BETWEEN :dateDebut AND :dateFin')
                ->setParameter('dateDebut', $dto->dateDebut)
                ->setParameter('dateFin', $dto->dateFin);
        } elseif ($dto->dateDebut) {
            $qb->andWhere('m.createdAt >= :dateDebut')
                ->setParameter('dateDebut', $dto->dateDebut);
        } elseif ($dto->dateFin) {
            $qb->andWhere('m.createdAt <= :dateFin')
                ->setParameter('dateFin', $dto->dateFin);
        }

        if ($dto->statut === 'finalise') {
            $qb->andWhere('m.dateValidation IS NOT NULL');
        } elseif ($dto->statut === 'en_cours') {
            $qb->andWhere('m.dateValidation IS NULL');
        }

        foreach ($orderCriteria->getField() as $index => $field) {
            if ($index === 0) {
                $qb->orderBy('m.' . $field, $orderCriteria->getDirection());
            } else {
                $qb->addOrderBy('m.' . $field, $orderCriteria->getDirection());
            }
        }

        $qb->setMaxResults($paginationCriteria->getLimit());

        return $qb->getQuery()->getResult();
    }
 
}
