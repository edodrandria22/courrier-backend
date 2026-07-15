<?php

namespace App\Service\courriers;

use App\Dto\courriers\RechercheCourriersDto;
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
    public function searchByDto(
        Utilisateurs $utilisateur,
        RechercheCourriersDto $dto,
        OrderCriteria $orderCriteria,
        PaginationCriteria $paginationCriteria
    ): array {
        return $this->repo->searchByDto($utilisateur, $dto, $orderCriteria, $paginationCriteria);
    }
    
    
}