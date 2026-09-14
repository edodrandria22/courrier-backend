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
            $conditions[] = new ConditionCriteria('object', $dto->object, 'ILIKE');
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
            [$debut, $fin] = $this->normalizeDateRange($dto->dateDebut, $dto->dateFin);
            $conditions[] = new ConditionCriteria('createdAt', [$debut, $fin], 'BETWEEN');
        } elseif ($dto->dateDebut) {
            $debut = (clone $dto->dateDebut)->format('Y-m-d') . ' 00:00:00';
            $conditions[] = new ConditionCriteria('createdAt', $debut, '>=');
        } elseif ($dto->dateFin) {
            $fin = (clone $dto->dateFin)->format('Y-m-d') . ' 23:59:59';
            $conditions[] = new ConditionCriteria('createdAt', $fin, '<=');
        }

        // Date reception BETWEEN
        if ($dto->dateReceptionDebut && $dto->dateReceptionFin) {
            [$debut, $fin] = $this->normalizeDateRange($dto->dateReceptionDebut, $dto->dateReceptionFin);
            $conditions[] = new ConditionCriteria('isReadAt', [$debut, $fin], 'BETWEEN');
        } elseif ($dto->dateReceptionDebut) {
            $debut = (clone $dto->dateReceptionDebut)->format('Y-m-d') . ' 00:00:00';
            $conditions[] = new ConditionCriteria('isReadAt', $debut, '>=');
        } elseif ($dto->dateReceptionFin) {
            $fin = (clone $dto->dateReceptionFin)->format('Y-m-d') . ' 23:59:59';
            $conditions[] = new ConditionCriteria('isReadAt', $fin, '<=');
        }

        // Date message BETWEEN
        if ($dto->dateMessageDebut && $dto->dateMessageFin) {
            [$debut, $fin] = $this->normalizeDateRange($dto->dateMessageDebut, $dto->dateMessageFin);
            $conditions[] = new ConditionCriteria('dateMessage', [$debut, $fin], 'BETWEEN');
        } elseif ($dto->dateMessageDebut) {
            $debut = (clone $dto->dateMessageDebut)->format('Y-m-d') . ' 00:00:00';
            $conditions[] = new ConditionCriteria('dateMessage', $debut, '>=');
        } elseif ($dto->dateMessageFin) {
            $fin = (clone $dto->dateMessageFin)->format('Y-m-d') . ' 23:59:59';
            $conditions[] = new ConditionCriteria('dateMessage', $fin, '<=');
        }

        // Statut basé sur dateValidation
        if ($this->notEmpty($dto->statut) && $dto->statut === 'finalise') {
            $conditions[] = new ConditionCriteria('dateValidation', null, 'IS NOT NULL');
        } elseif ($this->notEmpty($dto->statut) && $dto->statut === 'en_cours') {
            $conditions[] = new ConditionCriteria('dateValidation', null, 'IS NULL');
        }
        if($this->notEmpty($dto->bordureau)) {
            $conditions[] = new ConditionCriteria('bordureau',$dto->bordureau, 'LIKE');
        }

        return $this->search($conditions, $orderCriteria, $paginationCriteria);
    }

    /**
     * Normalise une plage de dates : début à 00:00:00, fin à 23:59:59
     *
     * @return array{0: \DateTimeInterface, 1: \DateTimeInterface}
     */
    private function normalizeDateRange(\DateTimeInterface $debut, \DateTimeInterface $fin): array
    {
        $debutNormalise = \DateTime::createFromFormat(
            'Y-m-d H:i:s',
            $debut->format('Y-m-d') . ' 00:00:00'
        );

        $finNormalisee = \DateTime::createFromFormat(
            'Y-m-d H:i:s',
            $fin->format('Y-m-d') . ' 23:59:59'
        );

        return [$debutNormalise, $finNormalisee];
    }

    public function searchByDtoUniqueReference(Utilisateurs $utilisateur, RechercheCourriersDto $dto, OrderCriteria $orderCriteria, PaginationCriteria $paginationCriteria): array
    {
        // Augmenter la limite pour compenser le filtrage par référence unique
        $originalLimit = $paginationCriteria->getLimit();
        $multiplier = 5; // Multiplier pour avoir assez de résultats après filtrage
        $paginationCriteria->setLimit($originalLimit * $multiplier);
        
        $results = $this->searchByDto($utilisateur, $dto, $orderCriteria, $paginationCriteria);
        
        $uniqueReferences = [];
        $filteredResults = [];
        
        foreach ($results as $result) {
            $reference = $result->getReference();
            if (!in_array($reference, $uniqueReferences, true)) {
                $uniqueReferences[] = $reference;
                $filteredResults[] = $result;
                
                // Arrêter si on a atteint la limite originale
                if (count($filteredResults) >= $originalLimit) {
                    break;
                }
            }
        }
        
        // Restaurer la limite originale
        $paginationCriteria->setLimit($originalLimit);
        
        return $filteredResults;
    }
    
}