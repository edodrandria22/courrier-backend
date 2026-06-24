<?php

namespace App\Service\utilisateurs;

use App\Dto\utils\ConditionCriteria;
use App\Dto\utils\OrderCriteria;
use App\Dto\utils\PaginationCriteria;
use App\Entity\utilisateurs\Utilisateurs;
use App\Repository\utilisateurs\VueUtilisateursRepository;
use App\Service\utils\BaseService;
use Doctrine\ORM\EntityManagerInterface;

class VueUtilisateursService extends BaseService
{
    private VueUtilisateursRepository $repository;
    
    public function __construct(
        private readonly VueUtilisateursRepository $repo,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
        $this->repository = $this->repo;
    }
    
    protected function getRepository()
    {
        return $this->repository;
    }
    public function rechercheByNomPrenom(Utilisateurs $user, string $nomPrenom, OrderCriteria $orderCriteria, PaginationCriteria $paginationCriteria): array
    {
        $nomJerena =  strtolower($nomPrenom);
        $conditions = [];
        $conditions[] = new ConditionCriteria('id', $user->getId(), '!=');
        $conditions[] = new ConditionCriteria('nomComplet', $nomJerena, 'like');
        $conditions[] = new ConditionCriteria('role', [2,3], 'in');
        $conditions[] = new ConditionCriteria('createdAt', $paginationCriteria->getValue(), '<');

        
        return $this->search($conditions, $orderCriteria, $paginationCriteria);
    }
    public function transformerArrayJson(array $array): array
    {
        $excludes = ["mdp","deletedAt","role","idRole","nomComplet"];
        foreach ($array as $key => $value) {
            
            $array[$key] = $value->toArray($excludes);
        }
        return $array;
    }
    
    
    
}