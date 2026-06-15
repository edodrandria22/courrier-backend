<?php

namespace App\Service\courriers;

use App\Dto\courriers\RechercheCourriersDto;
use App\Dto\utils\ConditionCriteria;
use App\Dto\utils\OrderCriteria;
use App\Dto\utils\PaginationCriteria;
use App\Entity\courriers\VueHistoriqueDetails;
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
        if ($utilisateur->getRole()->getName() !== 'Admin') {
            $conditions[] = new ConditionCriteria('utilisateurId', $utilisateur->getId(), '=');
        }
        else{
            $conditions[] = new ConditionCriteria('isSend', false, '=');
        }

        $conditions[] = new ConditionCriteria('createdAt', $paginationCriteria->getValue(), '<');

        if ($this->notEmpty($dto->reference)) {
            $conditions[] = new ConditionCriteria('reference',$dto->reference, 'LIKE');
        }

        if ($this->notEmpty($dto->object)) {
            $conditions[] = new ConditionCriteria('object', $dto->object, 'LIKE');
        }

        if ($this->notEmpty($dto->nom)) {
            $nom = mb_strtoupper($dto->nom, 'UTF-8');
            $conditions[] = new ConditionCriteria('nom', $nom, 'LIKE');
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
        if ($dto->isConfidentiel !== null) {
            $conditions[] = new ConditionCriteria('isConfidentiel', $dto->isConfidentiel, '=');
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
    public function tranformerUtilisateur(VueHistoriqueDetails $entite,array $exclude = []): array
    {
        
        $valiny = $entite->toArray($exclude); 
        $excludeUtilisateur = array_merge($exclude, ['mdp','id','idRole','role','createdAt']);

        $expediteur = $this->utilisateurService->cloneUtilisateur($this->utilisateurService->getById($entite->getExpediteurId() ?: 0));
          
        $destinataire = $this->utilisateurService->cloneUtilisateur($this->utilisateurService->getById($entite->getDestinataireId() ?: 0));
            
        $valiny['expediteur'] = $expediteur?->toArray($excludeUtilisateur) ?? null;
        $valiny['destinataire'] = $destinataire?->toArray($excludeUtilisateur) ?? null;
        return $valiny;
    }
    
    public function transformerArrayUtilisateur(array $entities, array $exclude = []): array
    {
        $valiny = [];
        for ($i = 0; $i < count($entities); $i++) {
            $valiny[$i] = $this->tranformerUtilisateur($entities[$i],$exclude); 
        }
        return $valiny;
    }
    public function getByIdCourrier(int $id): array{
        $conditions = [
            new ConditionCriteria('id', $id, '='),
            new ConditionCriteria('isSend', false, '='),
        ];
        return $this->search($conditions,new OrderCriteria('createdAt', 'desc'));
    }
    
    
    
}