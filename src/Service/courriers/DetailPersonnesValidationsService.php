<?php

namespace App\Service\courriers;

use App\Dto\utils\ConditionCriteria;
use App\Repository\courriers\DetailPersonnesValidationsRepository;
use App\Service\utils\BaseService;
use Doctrine\ORM\EntityManagerInterface;

class DetailPersonnesValidationsService extends BaseService
{
    public function __construct(
        private readonly DetailPersonnesValidationsRepository $repo,
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
    public function getByCourrierValidationId(int $courrierId): array
    {
        
         $conditions = [
            new ConditionCriteria('courrierValidation', $courrierId, '='),
        ];
        return $this->search($conditions);
    }
    public function deleteDetailPersonneValidation(int $courrierId): void
    {
        $detailPersonnes = $this->getByCourrierValidationId($courrierId);
        foreach ($detailPersonnes as $detailPersonne) {
            $this->delete($detailPersonne);
        }
    }
    
    
}