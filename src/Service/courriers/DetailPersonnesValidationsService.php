<?php

namespace App\Service\courriers;

use App\Dto\utils\ConditionCriteria;
use App\Entity\courriers\Courriers;
use App\Entity\courriers\DetailPersonnes;
use App\Entity\courriers\DetailPersonnesValidations;
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
    public function getByCourrierValidationId(int $courrierValidationId): array
    {
        
         $conditions = [
            new ConditionCriteria('courrierValidation', $courrierValidationId, '='),
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
    public function transformerEnDetailPersonne(DetailPersonnesValidations $detailPersonnesValidations,Courriers $courrier): DetailPersonnes
    {
        $employeur = $courrier->getCreateur()->getEmployeur();
        $detailPersonne = new DetailPersonnes();
        $detailPersonne->setCourrier($courrier);
        $detailPersonne->setName($detailPersonnesValidations->getName());
        $detailPersonne->setPrenom($detailPersonnesValidations->getPrenom());
        $detailPersonne->setEmail($detailPersonnesValidations->getEmail());
        $detailPersonne->setTelephone($detailPersonnesValidations->getTelephone());
        $detailPersonne->setMatricule($detailPersonnesValidations->getMatricule());
        $detailPersonne->setEntite($detailPersonnesValidations->getEntite());
        $detailPersonne->setEmployeur($employeur);
        return $detailPersonne;
    }
    
    
}