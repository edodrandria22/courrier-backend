<?php

namespace App\Service\courriers;

use App\Repository\courriers\EntitesRepository;
use App\Service\utils\BaseService;
use Doctrine\ORM\EntityManagerInterface;

class EntitesService extends BaseService
{
    public function __construct(
        private readonly EntitesRepository $repo,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
    }
    
    protected function getRepository()
    {
        return $this->repo;
    }
    
    
}