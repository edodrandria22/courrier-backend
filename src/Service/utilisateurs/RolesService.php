<?php

namespace App\Service\utilisateurs;

use App\Dto\utils\ConditionCriteria;
use App\Repository\utilisateurs\RolesRepository;
use App\Service\utils\BaseService;
use Doctrine\ORM\EntityManagerInterface;

class RolesService extends BaseService
{
    private RolesRepository $repository;
    
    public function __construct(
        private readonly RolesRepository $repo,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
        $this->repository = $this->repo;
    }
    
    protected function getRepository()
    {
        return $this->repository;
    }
    public function getByRole(array $roleIds): array
    {
        $conditions = [];
        $conditions[] = new ConditionCriteria('id', $roleIds, 'in');
        return $this->search($conditions);
    }
    
    
}