<?php

namespace App\Service\courriers;

use App\Dto\courriers\NumeroDepartDto;
use App\Dto\utils\ConditionCriteria;
use App\Dto\utils\OrderCriteria;
use App\Entity\courriers\NumeroCourriers;
use App\Entity\utilisateurs\Utilisateurs;
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
    public function getByUtilisateur(Utilisateurs $utilisateur,bool $isSend,int $annee): array
    {
        $dateDebut = new DateTimeImmutable("$annee-01-01 00:00:00");
        $dateFin = new DateTimeImmutable("$annee-12-31 23:59:59");
         $conditions = [
            new ConditionCriteria('utilisateur', $utilisateur->getId(), '='),
            new ConditionCriteria('isSend', $isSend, '='),
            new ConditionCriteria(
                'createdAt',
                [$dateDebut,$dateFin],
                'BETWEEN'
            )
        ];
        return $this->search($conditions,new OrderCriteria("createdAt","desc"));
    }
    public function getNumeroDepartActuel(Utilisateurs $utilisateur,bool $isSend,int $annee): NumeroCourriers
    {
 
        $numeroDepart = $this->getByUtilisateur($utilisateur, $isSend, $annee);
        if (empty($numeroDepart)) {
                  
            $valiny = new NumeroCourriers();
            $valiny->setNumero(0);
            $valiny->setIsSend($isSend);
            $valiny->setUtilisateur($utilisateur);
            return $valiny;
        }
        return $numeroDepart[0];
    }
    public function saveDto(Utilisateurs $utilisateur,NumeroDepartDto $dto): NumeroCourriers
    {
        $entity = new NumeroCourriers();
        $entity->setNumero($dto->getNumero());
        $entity->setIsSend($dto->getIsSend());
        $entity->setUtilisateur($utilisateur);
        $this->save($entity);
        return $entity;
    }
    
    
    
}