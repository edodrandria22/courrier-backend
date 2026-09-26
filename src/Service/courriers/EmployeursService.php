<?php

namespace App\Service\courriers;

use App\Repository\courriers\EmployeursRepository;
use App\Service\utils\BaseService;
use Doctrine\ORM\EntityManagerInterface;

class EmployeursService extends BaseService
{
    public function __construct(
        private readonly EmployeursRepository $repo,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
    }
    
    protected function getRepository()
    {
        return $this->repo;
    }
    
    
}