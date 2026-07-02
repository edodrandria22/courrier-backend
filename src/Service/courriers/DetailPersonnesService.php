<?php

namespace App\Service\courriers;

use App\Dto\utils\ConditionCriteria;
use App\Repository\courriers\DetailPersonnesRepository;
use App\Service\utils\BaseService;
use Doctrine\ORM\EntityManagerInterface;

class DetailPersonnesService extends BaseService
{
    public function __construct(
        private readonly DetailPersonnesRepository $repo,
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
    public function getByCourrierId(int $courrierId): array
    {
        
         $conditions = [
            new ConditionCriteria('courrier', $courrierId, '='),
        ];
        return $this->search($conditions);
    }
    
    
    
}