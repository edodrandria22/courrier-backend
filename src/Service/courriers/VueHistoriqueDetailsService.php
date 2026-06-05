<?php

namespace App\Service\courriers;

use App\Dto\courriers\RechercheCourriersDto;
use App\Dto\utils\ConditionCriteria;
use App\Dto\utils\OrderCriteria;
use App\Dto\utils\PaginationCriteria;
use App\Entity\utilisateurs\Utilisateurs;
use App\Repository\courriers\VueHistoriqueDetailsRepository;
use App\Service\utils\BaseService;
use App\Service\utilisateurs\UtilisateursService;
use Doctrine\ORM\EntityManagerInterface;

class VueHistoriqueDetailsService extends BaseService
{
    public function __construct(
        private readonly VueHistoriqueDetailsRepository $repo,
        private readonly UtilisateursService $utilisateurService,
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
    public function getHistoriques(Utilisateurs $user, OrderCriteria $orderCriteria,PaginationCriteria $paginationCriteria,bool $isSend): array
    {
        $conditions = [
            new ConditionCriteria('utilisateurId', $user->getId(), '='),
            new ConditionCriteria('createdAt', $paginationCriteria->getValue(), '<'),
            new ConditionCriteria('isSend', $isSend, '='),
        ];
        return $this->search($conditions, $orderCriteria, $paginationCriteria);
    }
    
    public function transformerArrayUtilisateur(array $entities, array $exclude = []): array
    {
        $valiny = [];
        $excludeUtilisateur = array_merge($exclude, ['mdp','id','idRole','role','createdAt']);
        for ($i = 0; $i < count($entities); $i++) {
            $valiny[$i] = $entities[$i]->toArray($exclude); 

            $expediteur = $this->utilisateurService->getById($entities[$i]->getExpediteurId() ?: 0);
            $destinataire = $this->utilisateurService->getById($entities[$i]->getDestinataireId() ?: 0);
            $valiny[$i]['expediteur'] = $expediteur?->toArray($excludeUtilisateur) ?? null;
            $valiny[$i]['destinataire'] = $destinataire?->toArray($excludeUtilisateur) ?? null;
        }
        return $valiny;
    }
    
    
    
}