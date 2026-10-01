<?php

namespace App\Service\courriers;

use App\Dto\utils\OrderCriteria;
use App\Repository\courriers\CourrierValidationsRepository;
use App\Service\utils\BaseService;
use Doctrine\ORM\EntityManagerInterface;
use App\Dto\courriers\CourriersValidationsDto;
use App\Dto\utils\ConditionCriteria;
use App\Dto\utils\PaginationCriteria;
use App\Entity\courriers\Courriers;
use App\Entity\courriers\CourrierValidations;
use App\Entity\courriers\DetailPersonnesValidations;
use App\Entity\utilisateurs\Utilisateurs;
use App\Service\messages\MessagesService;
use App\Service\utils\FichiersValidationsService;
use Exception;
class CourrierValidationsService extends BaseService
{
    public function __construct(
        private readonly CourrierValidationsRepository $repo,
        EntityManagerInterface $entityManager,
        private readonly DetailPersonnesValidationsService $detailPersonnesValidationsService,
        private readonly EntitesService $entitesService,
        private readonly FichiersValidationsService $fichiersValidationsService,
        private readonly CourriersService $courriersService,
        private readonly MessagesService $messagesService
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
            $this->fichiersValidationsService->persistFiles($fichiers, $courrierValidation, false);

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
    public function addRemarque(int $id, string $remarque): CourrierValidations
    {
        $courrierValidation = $this->getVerifierById($id);
        $courrierValidation->setObservationSuperviseur($remarque);
        return $this->save($courrierValidation);
    }
    private const MOIS = [
        1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril',
        5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août',
        9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre',
    ];

    private function getDescription(CourrierValidations $courrierValidation): string
    {
        $periode = $this->formatPeriode(
            $courrierValidation->getDateDebut(),
            $courrierValidation->getDateFin()
        );

        return "Demande d'ordre de mission à " . $courrierValidation->getVille() . " " . $periode;
    }

    private function formatPeriode(\DateTimeInterface $debut, \DateTimeInterface $fin): string
    {
        $memeAnnee = $debut->format('Y') === $fin->format('Y');
        $memeMois  = $debut->format('n') === $fin->format('n');

        if ($memeAnnee && $memeMois) {
            // du 10 au 25 avril 2025
            return sprintf(
                'du %d au %s',
                (int) $debut->format('j'),
                $this->formatDate($fin)
            );
        }

        if ($memeAnnee) {
            // du 28 avril au 5 mai 2025
            return sprintf(
                'du %d %s au %s',
                (int) $debut->format('j'),
                self::MOIS[(int) $debut->format('n')],
                $this->formatDate($fin)
            );
        }

        // du 28 décembre 2025 au 3 janvier 2026
        return sprintf('le %s au %s', $this->formatDate($debut), $this->formatDate($fin));
    }

    private function formatDate(\DateTimeInterface $date): string
    {
        return sprintf(
            '%d %s %s',
            (int) $date->format('j'),
            self::MOIS[(int) $date->format('n')],
            $date->format('Y')
        );
    }
    private function transformerEnCourrier(CourrierValidations $courrierValidation): Courriers
    {
        if($courrierValidation->getDateValidation()!== null){
            throw new Exception("Le courrier de validation a déjà été validé");
        }
        $courrier = new Courriers();
        $courrier->setObject($courrierValidation->getObject());
        $courrier->setReference($this->courriersService->generateReference());
        $courrier->setIsConfidentiel(false);
        $courrier->setCreateur($courrierValidation->getCreateur());
        $courrier->setDescription($this->getDescription($courrierValidation));
        $detailsPersonnesValidation = $this->detailPersonnesValidationsService->getByCourrierValidationId($courrierValidation->getId());
        foreach ($detailsPersonnesValidation as $detailPersonneValidation) {
            $detailPersonne = $this->detailPersonnesValidationsService->transformerEnDetailPersonne($detailPersonneValidation, $courrier);
            $courrier->addDetailPersonne($detailPersonne);
        }
        $this->save($courrier);
        return $courrier;
    }
    public function validerCourrierValidation(int $id): CourrierValidations
    {
        $this->em->getConnection()->beginTransaction();
        try{
            $courrierValidation = $this->getVerifierById($id);
            $courrier = $this->transformerEnCourrier($courrierValidation);
            $courrierValidation->setDateValidation(new \DateTimeImmutable());
            $courrierValidation = $this->save($courrierValidation);
            $files = $this->fichiersValidationsService->getByCourrierValidationIdUploaded($courrierValidation->getId());
            $this->messagesService->tranfererOmChezSag($courrier,$courrierValidation->getObservation(),null,$courrierValidation->getNumeroDepart(),$files);
            $this->em->getConnection()->commit();
            return $courrierValidation;
        } catch (Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }
    }
    public function getByUser(Utilisateurs $user, ?\DateTimeImmutable $date = null, int $limit = 10, ?string $isValid = null): array
    {
        $pagination = new PaginationCriteria($date, $limit);
        $conditions = [
            new ConditionCriteria('createur', $user->getId(), '='),
            new ConditionCriteria('createdAt', $pagination->getValue(), '<'),
        ];
        $isValidBool = $this->parseIsValid($isValid);
        if ($isValidBool !== null) {
            $conditions[] = new ConditionCriteria('dateValidation', $isValidBool ? null : null, $isValidBool ? 'IS NOT NULL' : 'IS NULL');
        }
        return $this->search($conditions, new OrderCriteria(), $pagination);
    }

    private function parseIsValid(?string $isValid): ?bool
    {
        return match($isValid) {
            'true' => true,
            'false' => false,
            default => null,
        };
    }
    public function tranformerEnJson(CourrierValidations $courrierValidation):array{
        $excludes = ["deletedAt"];
        $excludesFiles = [...$excludes, "createdAt"];
        $result = $courrierValidation->toArray($excludes);
        $listeFiles = $this->fichiersValidationsService->getByCourrierValidationId($courrierValidation->getId());
        $detailPersonnes = $this->detailPersonnesValidationsService->getByCourrierValidationId($courrierValidation->getId());
        $result['files'] = $this->fichiersValidationsService->transformerArray($listeFiles,$excludesFiles);
        $result['detailPersonnes'] = $this->detailPersonnesValidationsService->transformerArray($detailPersonnes,[...$excludesFiles,"id"]);
        return $result;
    }
    public function transformerArrayEnJson(array $array):array{
        $result = [];
        foreach($array as $item){
            $result[] = $this->tranformerEnJson($item);
        }
        return $result;
    }
    
    
}