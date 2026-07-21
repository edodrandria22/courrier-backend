<?php

namespace App\Service\courriers;

use App\Dto\courriers\RechercheCourriersDto;
use App\Dto\utils\ConditionCriteria;
use App\Dto\utils\OrderCriteria;
use App\Dto\utils\PaginationCriteria;
use App\Entity\utilisateurs\Utilisateurs;
use App\Repository\courriers\VueHistoriqueDetailPersonnesRepository;
use App\Service\utils\BaseService;
use Doctrine\ORM\EntityManagerInterface;

class VueHistoriqueDetailPersonnesService extends BaseService
{
    public function __construct(
        private readonly VueHistoriqueDetailPersonnesRepository $repo,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
    }
    
    protected function getRepository()
    {
        return $this->repo;
    }
    private function notEmpty(?string $value): bool
    {
        return $value !== null && trim($value) !== '';
    }
    public function searchByDto(Utilisateurs $utilisateur, RechercheCourriersDto $dto, OrderCriteria $orderCriteria, PaginationCriteria $paginationCriteria): array
    {
        $conditions = [];
        if ($utilisateur->getRole()->getName() !== 'Admin') {
            $conditions[] = new ConditionCriteria('utilisateurId', $utilisateur->getId(), '=');
        }
        else{
            $conditions[] = new ConditionCriteria('isSend', false, '=');
        }

        $conditions[] = new ConditionCriteria('dateMessage', $paginationCriteria->getValue(), '<');

        if ($this->notEmpty($dto->reference)) {
            $conditions[] = new ConditionCriteria('reference',$dto->reference, 'LIKE');
        }

        if ($this->notEmpty($dto->object)) {
            $conditions[] = new ConditionCriteria('object', $dto->object, 'LIKE');
        }

        if ($this->notEmpty($dto->nom)) {
            $nom = mb_strtoupper($dto->nom, 'UTF-8');
            $conditions[] = new ConditionCriteria('name', $nom, 'LIKE');
        }

        if ($this->notEmpty($dto->prenom)) {
            $prenom = $dto->prenom ? mb_convert_case($dto->prenom, MB_CASE_TITLE, "UTF-8") : null;
            $conditions[] = new ConditionCriteria('prenom', $prenom, 'LIKE');
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
        if ($this->notEmpty($dto->numeroExpediteur)) {
            $conditions[] = new ConditionCriteria('numeroExpediteur', $dto->numeroExpediteur, '=');
        }
        if ($this->notEmpty($dto->numeroDestinataire)) {
            $conditions[] = new ConditionCriteria('numeroDestinataire', $dto->numeroDestinataire, '=');
        }
        
        if ($dto->isConfidentiel !== null) {
            $conditions[] = new ConditionCriteria('isConfidentiel', $dto->isConfidentiel, '=');
        }

        // Date courrier BETWEEN
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

        // Date reception BETWEEN
        if ($dto->dateReceptionDebut && $dto->dateReceptionFin) {
            $conditions[] = new ConditionCriteria(
                'isReadAt',
                [$dto->dateReceptionDebut, $dto->dateReceptionFin],
                'BETWEEN'
            );
        } elseif ($dto->dateReceptionDebut) {
            $conditions[] = new ConditionCriteria('isReadAt', $dto->dateReceptionDebut, '>=');
        } elseif ($dto->dateReceptionFin) {
            $conditions[] = new ConditionCriteria('isReadAt', $dto->dateReceptionFin, '<=');
        }

        // Date message BETWEEN
        if ($dto->dateMessageDebut && $dto->dateMessageFin) {
            $conditions[] = new ConditionCriteria(
                'dateMessage',
                [$dto->dateMessageDebut, $dto->dateMessageFin],
                'BETWEEN'
            );
        } elseif ($dto->dateMessageDebut) {
            $conditions[] = new ConditionCriteria('dateMessage', $dto->dateMessageDebut, '>=');
        } elseif ($dto->dateMessageFin) {
            $conditions[] = new ConditionCriteria('dateMessage', $dto->dateMessageFin, '<=');
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