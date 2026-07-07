<?php

namespace App\Service\courriers;

use App\Dto\utils\ConditionCriteria;
use App\Repository\courriers\NumeroCourriersRepository;
use App\Service\utils\BaseService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class NumeroCourriersService extends BaseService
{
    public function __construct(
        private readonly NumeroCourriersRepository $repo,
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
    public function getByUtilisateurId(int $utilisateurId,bool $isSend,int $annee): array
    {
        $dateDebut = new DateTimeImmutable("$annee-01-01 00:00:00");
        $dateFin = new DateTimeImmutable("$annee-12-31 23:59:59");
         $conditions = [
            new ConditionCriteria('utilisateur', $utilisateurId, '='),
            new ConditionCriteria('isSend', $isSend, '='),
            new ConditionCriteria(
                'createdAt',
                [$dateDebut,$dateFin],
                'BETWEEN'
            )
        ];
        return $this->search($conditions);
    }
    
    
    
}