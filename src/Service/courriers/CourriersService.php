<?php

namespace App\Service\courriers;

use App\Dto\utils\ConditionCriteria;
use App\Dto\utils\OrderCriteria;
use App\Dto\utils\PaginationCriteria;
use App\Entity\courriers\Courriers;
use App\Entity\utilisateurs\Utilisateurs;
use App\Repository\courriers\CourriersRepository;
use App\Service\utils\BaseService;
use App\Service\utils\MailService;
use App\Service\utils\ValidationService;
use Doctrine\ORM\EntityManagerInterface;
use App\Dto\courriers\CourriersDto;
use App\Entity\courriers\VueHistoriqueDetails;
use App\Service\mercure\MercureService;
use App\Service\messages\HistoriquesService;

class CourriersService extends BaseService
{
    public function __construct(
        private readonly CourriersRepository $repo,
        private readonly ValidationService $validator,
        EntityManagerInterface $entityManager,
        private readonly MailService $mailService,
        private readonly VueHistoriqueDetailsService $vueHistoriqueDetailsService,
        private readonly MercureService $mercureService,
        private readonly HistoriquesService $historiquesService
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
    public function generateReference(): string
    {
        $date = new \DateTimeImmutable();
        $dateStr = $date->format('dmY');
        $count = $this->repo->countDailyCourriers($date);

        return $dateStr . '/REF' . ($count + 1);
    }
    public function countYearlyCourriers(?int $annee= null): int
    {
        $date = new \DateTimeImmutable();
        if ($annee === null) {
            $annee = $date->format('Y');
        }
        return $this->repo->countYearlyCourriers($annee);
    }
    

    /**
     * Récupère un courrier par son ID
     */
    public function getCourrierById(int $id): ?Courriers
    {
        return $this->getById($id);
    }
    public function getValidatedCourrier(int $courrierId): Courriers
    {
        $courrier = $this->getCourrierById($courrierId);
        $this->validator->throwIfNull($courrier, "Courrier avec l'ID $courrierId introuvable.");
        return $courrier;
    }

    /**
     * Supprime logiquement un courrier
     */
    public function supprimerCourrier(int $id): void
    {
        $courrier = $this->getValidatedCourrier($id);

        $courrier->delete();
        $this->save($courrier);
    }

    public function cloturerCourrier(int $id,Utilisateurs $user): object
    {
         $conn = $this->em->getConnection();
        $conn->beginTransaction(); // Début de la transaction
        try {
            $courrier = $this->getValidatedCourrier($id);
            $courrier->setDateValidation(new \DateTimeImmutable());
            $courrier->setCloturePar($user);
            $destinataire = $courrier->getEmail();
            $subject = "Cloturation du courrier chez Espa";
            $this->mailService->sendEmail($destinataire, $subject, $this->genererDivClorer($courrier, $user));
            $excludes = [ 'deletedAt'];
            $data = $courrier->toArray($excludes);
            $this->mercureService->sendNotification("clotureCourrier",$data);
            $valiny= parent::save($courrier);

            $conn->commit(); // Commit de la transaction
            return $valiny;
          
        } catch (\Throwable $th) {
            $conn->rollBack(); // Rollback de la transaction
            throw $th;
        }

    }

    /**
     * Sauvegarde un courrier avec génération de référence si nécessaire
     * 
     * @param Courriers $courrier
     */
    public function save(object $courrier, bool $flush = true): object
    {
        // $this->validator->throwIfNull($courrier->getNom(), "Le nom du déposant est obligatoire.");

        $conn = $this->em->getConnection();
        $conn->beginTransaction(); // Début de la transaction

        try {
            // Génération de la référence si elle est nulle
            $courrier->setReference($courrier->getReference() ?? $this->generateReference());
            // Normalisation des identités
            $courrier->setNom($courrier->getNom() ? mb_strtoupper($courrier->getNom()) : null);
            $courrier->setPrenom($courrier->getPrenom() ? mb_convert_case($courrier->getPrenom(), MB_CASE_TITLE) : null);

            // Persistance via la méthode parent
            $savedEntity = parent::save($courrier, $flush);

            $conn->commit(); // Commit de la transaction

            return $savedEntity;

        } catch (\Throwable $e) {
            $conn->rollBack(); // Annule la transaction en cas d'erreur
            throw $e; // Relance l'exception pour la remonter
        }
    }
    public function saveDto(Utilisateurs $utilisateur,CourriersDto $dto): Courriers
    {
        $courrier = new Courriers();
        $object = $dto->getIsConfidentiel() ? "Pli fermé" : $dto->getObject();
        $courrier->setObject($object);
        $courrier->setIsConfidentiel($dto->getIsConfidentiel());
        if(!$dto->getIsConfidentiel()){
            $courrier->setDescription($dto->getDescription());
            $courrier->setNom($dto->getNom());
            $courrier->setPrenom($dto->getPrenom());
            $courrier->setTelephone($dto->getTelephone());
        }
        $courrier->setEmail($dto->getEmail());
        
        $courrier->setCreateur($utilisateur);
        

        $result = $this->save($courrier);
        return $result;
    }
    public function updateDto(Utilisateurs $utilisateur,Courriers $courrier, CourriersDto $dto): Courriers
    {
        if($courrier->getCreateur()->getId() != $utilisateur->getId()){
            throw new \Exception("Seule l'auteur du courrier peut le modifier son courrier");
        }
        if ($courrier->getDateMessage()) {
            throw new \Exception("Le courrier a déjà été envoyé, vous ne pouvez plus le modifier");
        }
        $object = $dto->getIsConfidentiel() ? "Pli fermé" : $dto->getObject();
        $courrier->setObject($object);
        $courrier->setIsConfidentiel($dto->getIsConfidentiel());
        if(!$dto->getIsConfidentiel()){
            $courrier->setDescription($dto->getDescription());
            $courrier->setNom($dto->getNom());
            $courrier->setPrenom($dto->getPrenom());
            $courrier->setTelephone($dto->getTelephone());
        }
        $courrier->setEmail($dto->getEmail());
        
        $result = $this->save($courrier);
        return $result;
    }
    public function updateDtoId(Utilisateurs $utilisateur,int $id, CourriersDto $dto): Courriers
    {
        $courrier = $this->getVerifierById($id);
        return $this->updateDto($utilisateur, $courrier, $dto);
    }

   
    public function getAllCourierDisponible(Utilisateurs $utilisateurs): array
    {
        return $this->repo->getAllCourierDisponible($utilisateurs);
    }
    public function getAllByUser(Utilisateurs $user,OrderCriteria $orderCriteria,PaginationCriteria $paginationCriteria,bool $isSend): array
    {
        $result = $this->vueHistoriqueDetailsService->getHistoriques($user, $orderCriteria,$paginationCriteria,$isSend);
        return $result;
    }
    public function getAllByUserJson(Utilisateurs $user,OrderCriteria $orderCriteria,PaginationCriteria $paginationCriteria,bool $isSend): array
    {
        $exclude = ['deletedAt','utilisateurId','destinataireId','expediteurId'];
        $historique = $this->getAllByUser($user, $orderCriteria, $paginationCriteria, $isSend);
        return $this->vueHistoriqueDetailsService->transformerArrayUtilisateur($historique, $exclude);
    }

    public function cloneCourrier(Courriers $courrierOriginal): Courriers
    {
        $clone = new Courriers();
        $clone->setId($courrierOriginal->getId());
        $clone->setReference($courrierOriginal->getReference()); // ou générer une nouvelle référence
        $clone->setObject($courrierOriginal->getObject());
        $clone->setDescription($courrierOriginal->getDescription());
        $clone->setEmail($courrierOriginal->getEmail());
        $clone->setNom($courrierOriginal->getNom());
        $clone->setPrenom($courrierOriginal->getPrenom());
        $clone->setTelephone($courrierOriginal->getTelephone());
        $clone->setDateMessage($courrierOriginal->getDateMessage());
        $clone->setCreateur($courrierOriginal->getCreateur());
        $clone->setCreatedAt($courrierOriginal->getCreatedAt());
        $clone->setCloturePar($courrierOriginal->getCloturePar()); // le clone n’est pas encore clôturé
        return $clone;
    }
    public function genererDivClorer(Courriers $courrier,Utilisateurs $utilisateur)
    {
        $nom = $courrier->getNom() . ' ' . $courrier->getPrenom();
        $messageHtml = "Votre courier est cloturer , vous devier allez a la porte ".$utilisateur->getAdresse();
        return $this->mailService->getHtmlMail($nom, $messageHtml);
    }
    /**
     * @param string $reference
     * @return Courriers|null
     */
    public function getByReference(string $reference)
    {
        $conditions = [
            new ConditionCriteria('reference', $reference, '='),
        ];


        $valiny = $this->search($conditions)[0] ?? null;
        
        return $valiny;
    }
    public function genererEmailSuiviMessage(Courriers $courrier,array $listeMessage)
    {
        if ($courrier->getEmail() === null) {
            return ;
        }
        $nom = $courrier->getNom() . ' ' . $courrier->getPrenom();
        $listDiv = "";
        foreach ($listeMessage as $message) {
          $listDiv .= $message->getParticipantsHtml();  
        }
        $message=$this->mailService->getHtmlMail($nom, $listDiv);
        $this->mailService->sendEmail($courrier->getEmail(), "Suivi du courier", $message);
    }
    public function modifierObservationHistorique(int $idHistorique,Utilisateurs $utilisateur ,?string $observation): ?VueHistoriqueDetails
    {
        $this->historiquesService->modifierObservation($idHistorique, $utilisateur, $observation);
        return $this->vueHistoriqueDetailsService->getByHistoriqueId($idHistorique);
    }

    
    
}
