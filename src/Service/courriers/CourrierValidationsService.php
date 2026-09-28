<?php

namespace App\Service\courriers;

use App\Repository\courriers\CourrierValidationsRepository;
use App\Service\utils\BaseService;
use Doctrine\ORM\EntityManagerInterface;
use App\Dto\courriers\CourriersValidationsDto;
use App\Entity\courriers\CourrierValidations;
use App\Entity\courriers\DetailPersonnesValidations;
use App\Entity\utilisateurs\Utilisateurs;
use App\Service\utils\FichiersValidationsService;
use Exception;
class CourrierValidationsService extends BaseService
{
    public function __construct(
        private readonly CourrierValidationsRepository $repo,
        EntityManagerInterface $entityManager,
        private readonly DetailPersonnesValidationsService $detailPersonnesValidationsService,
        private readonly EntitesService $entitesService,
        private readonly FichiersValidationsService $fichiersValidationsService
    ) {
        parent::__construct($entityManager);
    }
    
    protected function getRepository()
    {
        return $this->repo;
    }
    public function genererListeDetailPersonne(CourriersValidationsDto $dto,CourrierValidations $courrierValidation): void
    {
        foreach ($dto->getDetailPersonnes() as $detailPersonne) {
            $nom = $detailPersonne->getName() ? mb_strtoupper($detailPersonne->getName()) : null;
            $prenom = $detailPersonne->getPrenom() ? mb_convert_case($detailPersonne->getPrenom(), MB_CASE_TITLE) : null;

            $detailPersonneEntity = new DetailPersonnesValidations();
            $detailPersonneEntity->setCourrierValidation($courrierValidation);
            $detailPersonneEntity->setName($nom);
            $detailPersonneEntity->setPrenom($prenom);
            $detailPersonneEntity->setEmail($detailPersonne->getEmail());
            $detailPersonneEntity->setTelephone($detailPersonne->getTelephone());
            $detailPersonneEntity->setMatricule($detailPersonne->getMatricule());

            $entite = $this->entitesService->getVerifierById($detailPersonne->getEntiteId());
            $detailPersonneEntity->setEntite($entite);

            
            $courrierValidation->addDetailPersonne($detailPersonneEntity);
        }
        
    }
    public function saveDto(Utilisateurs $utilisateur,CourriersValidationsDto $dto, array $fichiers = []): CourrierValidations
    {
        $this->em->getConnection()->beginTransaction();
        try {
            $courrierValidation = new CourrierValidations();
            $courrierValidation->setObject($dto->getObject());
            $courrierValidation->setVille($dto->getVille());
            $courrierValidation->setDateDebut($dto->getDateDebut());
            $courrierValidation->setDateFin($dto->getDateFin());
            $courrierValidation->setObservation($dto->getObservation());
            $courrierValidation->setNumeroDepart($dto->getNumeroDepart());
            $this->genererListeDetailPersonne($dto, $courrierValidation);
            $courrierValidation->setCreateur($utilisateur);
            $result = $this->save($courrierValidation);
            $courrierValidation->setOriginId($result->getId());
            $result = $this->save($courrierValidation);
            $this->fichiersValidationsService->persistFiles($fichiers, $courrierValidation);

            $this->em->getConnection()->commit();
            return $result;
            
        } catch (Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }
    }
    // public function updateDto(Utilisateurs $utilisateur,CourrierValidations $courrierValidation, CourriersValidationsDto $dto, array $fichiers = []): CourrierValidations
    // {
    //     $this->em->getConnection()->beginTransaction();
    //     try {
    //         if($courrierValidation->getCreateur()->getId() != $utilisateur->getId()){
    //             throw new Exception("Seule l'auteur du courrier peut le modifier son courrier");
    //         }
    //         if ($courrierValidation->getDateValidation()) {
    //             throw new Exception("Le courrier a déjà été validé, vous ne pouvez plus le modifier");
    //         }
    //         $courrierValidation->setObject($dto->getObject());
    //         $courrierValidation->setVille($dto->getVille());
    //         $courrierValidation->setDateDebut($dto->getDateDebut());
    //         $courrierValidation->setDateFin($dto->getDateFin());
    //         $courrierValidation->setObservation($dto->getObservation());
    //         $courrierValidation->setNumeroDepart($dto->getNumeroDepart());
    //         $this->detailPersonnesValidationsService->deleteDetailPersonneValidation($courrierValidation->getId());
    //         $this->genererListeDetailPersonne($dto, $courrierValidation);
    //         $result = $this->save($courrierValidation);
    //         $this->fichiersValidationsService->persistFiles($fichiers, $courrierValidation);
    //         $this->em->getConnection()->commit();
    //         return $result;
    //     } catch (Exception $e) {
    //         $this->em->getConnection()->rollBack();
    //         throw $e;
    //     }
    // }
    public function updateDto(Utilisateurs $utilisateur,CourrierValidations $oldCourrierValidation, CourriersValidationsDto $dto, array $fichiers = []): CourrierValidations
    {
        $this->em->getConnection()->beginTransaction();
        try {
            if($oldCourrierValidation->getCreateur()->getId() != $utilisateur->getId()){
                throw new Exception("Seule l'auteur du courrier peut le modifier son courrier");
            }
            if ($oldCourrierValidation->getDateValidation()) {
                throw new Exception("Le courrier a déjà été validé, vous ne pouvez plus le modifier");
            }
            $courrierValidation = new CourrierValidations();
            $courrierValidation->setObject($dto->getObject());
            $courrierValidation->setVille($dto->getVille());
            $courrierValidation->setDateDebut($dto->getDateDebut());
            $courrierValidation->setDateFin($dto->getDateFin());
            $courrierValidation->setObservation($dto->getObservation());
            $courrierValidation->setNumeroDepart($dto->getNumeroDepart());
            $this->genererListeDetailPersonne($dto, $courrierValidation);
            $courrierValidation->setCreateur($utilisateur);
            $result = $this->save($courrierValidation);
            $courrierValidation->setOriginId($oldCourrierValidation->getOriginId());
            $result = $this->save($courrierValidation);
            $this->fichiersValidationsService->persistFiles($fichiers, $courrierValidation);
            $this->delete($oldCourrierValidation);
            $this->em->getConnection()->commit();
            return $result;

        } catch (Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }
    }
    public function updateDtoId(Utilisateurs $utilisateur,int $id, CourriersValidationsDto $dto, array $fichiers = []): CourrierValidations
    {
        $courrierValidation = $this->getVerifierById($id);
        return $this->updateDto($utilisateur, $courrierValidation, $dto, $fichiers);
    }
    
    
}