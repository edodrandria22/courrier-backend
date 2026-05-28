<?php

namespace App\Service\courriers;

use App\Dto\courriers\RechercheCourriersDto;
use App\Dto\utils\ConditionCriteria;
use App\Dto\utils\OrderCriteria;
use App\Dto\utils\PaginationCriteria;
use App\Entity\utilisateurs\Utilisateurs;
use App\Repository\courriers\VueHistoriqueDetailsRepository;
use App\Service\utils\BaseService;
use Doctrine\ORM\EntityManagerInterface;


class VueHistoriqueDetailsService extends BaseService
{
    public function __construct(
        private readonly VueHistoriqueDetailsRepository $repo,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
    }
    
    protected function getRepository()
    {
        return $this->repo;
    }
    /**
     * Génère une référence automatique au format JJMMAAAA/REFN
     */
    

    private function notEmpty(?string $value): bool
    {
        return $value !== null && trim($value) !== '';
    }
    public function searchByDto(Utilisateurs $utilisateur, RechercheCourriersDto $dto, OrderCriteria $orderCriteria, PaginationCriteria $paginationCriteria): array
    {
        $conditions = [];
        $conditions[] = new ConditionCriteria('utilisateurId', $utilisateur->getId(), '=');

        $conditions[] = new ConditionCriteria('createdAt', $paginationCriteria->getValue(), '<');

        if ($this->notEmpty($dto->reference)) {
            $conditions[] = new ConditionCriteria('reference',$dto->reference, 'LIKE');
        }

        if ($this->notEmpty($dto->object)) {
            $conditions[] = new ConditionCriteria('object', $dto->object, 'LIKE');
        }

        if ($this->notEmpty($dto->nom)) {
            $conditions[] = new ConditionCriteria('nom', $dto->nom, 'LIKE');
        }

        if ($this->notEmpty($dto->prenom)) {
            $conditions[] = new ConditionCriteria('prenom', $dto->prenom, 'LIKE');
        }

        if ($this->notEmpty($dto->email)) {
            $conditions[] = new ConditionCriteria('email', $dto->email, 'LIKE');
        }

        if ($this->notEmpty($dto->telephone)) {
            $conditions[] = new ConditionCriteria('telephone', $dto->telephone, 'LIKE');
        }

        if ($this->notEmpty($dto->numero)) {
            $conditions[] = new ConditionCriteria('numero', $dto->numero, '=');
        }

        // Date BETWEEN
        if ($dto->dateDebut && $dto->dateFin) {
            $conditions[] = new ConditionCriteria(
                'createdAt',
                [$dto->dateDebut, $dto->dateFin],
                'BETWEEN'
            );
        } elseif ($dto->dateDebut) {
            $conditions[] = new ConditionCriteria('createdAt', $dto->dateDebut, '>=');
        } elseif ($dto->dateFin) {
            $conditions[] = new ConditionCriteria('createdAt', $dto->dateFin, '<=');
        }

        // Statut basé sur dateValidation
        if ($this->notEmpty($dto->statut) && $dto->statut === 'finalise') {
            $conditions[] = new ConditionCriteria('dateValidation', null, 'IS NOT NULL');
        } elseif ($this->notEmpty($dto->statut) && $dto->statut === 'en_cours') {
            $conditions[] = new ConditionCriteria('dateValidation', null, 'IS NULL');
        }

        
        return $this->search($conditions, $orderCriteria, $paginationCriteria);
    }

    
    
    
}