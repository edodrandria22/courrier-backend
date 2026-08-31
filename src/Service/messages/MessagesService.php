<?php

namespace App\Service\messages;

use App\Dto\courriers\CourriersDto;
use App\Dto\utils\ConditionCriteria;
use App\Dto\utils\JoinCriteria;
use App\Dto\utils\OrderCriteria;
use App\Dto\utils\PaginationCriteria;
use App\Entity\courriers\Courriers;
use App\Entity\messages\Messages;
use App\Entity\utilisateurs\Utilisateurs;
use App\Repository\messages\MessagesRepository;
use App\Service\courriers\CourriersService;
use App\Service\courriers\VueHistoriqueDetailsService;
use App\Service\mercure\MercureService;
use App\Service\utilisateurs\UtilisateursService;
use App\Service\utils\BaseService;
use App\Service\utils\ValidationService;
use DateTimeImmutable;
use Exception;
use Doctrine\ORM\EntityManagerInterface;

use App\Service\utils\FichiersService;
use App\Service\utils\MailService;
use Override;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MessagesService extends BaseService
{
    public function __construct(
        private readonly MessagesRepository $repo,
        private readonly UtilisateursService $utilisateursService,
        private readonly CourriersService $courriersService,
        EntityManagerInterface $em,
        private readonly FichiersService $fichiersService,
        private readonly ValidationService $validationService,
        private readonly HistoriquesService $historiquesService,
        private readonly VueHistoriqueDetailsService $vueHistoriqueDetailService,
        private readonly MercureService $mercureService,
        private readonly MailService $mailService,
    ) {
        parent::__construct($em);
    }  
    public function getRepository()
    {
        return $this->repo;
    }
    private function getValidatedMessage(int $messageId): Messages
    {
        $message = $this->getById($messageId);
        $this->validationService->throwIfNull($message, "Message avec l'ID $messageId introuvable.");
        return $message;
    }
    private function createMessage(
        ?Utilisateurs $expediteur,
        Utilisateurs $destinataire,
        Courriers $courrier,
        ?string $observation,
        ?string $bordureau
    ): Messages {
        $message = new Messages();
        $message->setExpediteur($expediteur);
        $message->setDestinataire($destinataire);
        $message->setCourrier($courrier);
        $message->setObservation($observation);
        $message->setBordureau($bordureau);
        if (!$courrier->getDateMessage()) {
            $courrier->setDateMessage(new DateTimeImmutable());
            $this->save($courrier);
        }
        $message->setIsReadAt(null);
        

        $this->save($message);

        return $message;
    }
    /**
     * Envoie un message concernant un courrier (avec upload optionnel de fichiers)
     * 
     * @param UploadedFile[] $files
     */
    public function envoyerMessage(
        int $expId,
        int $destId,
        int $courrierId,
        ?string $observation = null,
        ?string $bordureau = null,
        array $files = []
    ): Messages {
        $this->em->getConnection()->beginTransaction();

        try {
            // Récupération et validation des entités
            $expediteur = $this->utilisateursService->getValidatedUser($expId, "Expéditeur");
            $destinataire = $this->utilisateursService->getValidatedUser($destId, "Destinataire");
            $courrier = $this->courriersService->getValidatedCourrier($courrierId);
                // Création et persistance du message
            $message = $this->createMessage($expediteur, $destinataire, $courrier, $observation,$bordureau);
            // Persistance des fichiers liés
            $this->fichiersService->persistFiles($files, $message);
            // $this->historiquesService->tranformerMessageEnHistorique($message);
            
            $this->em->getConnection()->commit();

            return $message;

        } catch (\Throwable $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }
    }
    /**
     * Envoie un message concernant un courrier (avec upload optionnel de fichiers)
     * 
     * @param UploadedFile[] $files
     */
    public function envoyerNouvelleMessage(
        Utilisateurs $utilisateur,
        Courriers $courrier,
        ?string $observation,
        ?int $numeroArrive,
        array $files = [],
    ): Messages {
        try {
            // Récupération et validation des entités
           
            if ($courrier->getDateMessage()) {
                throw new Exception('Courrier deja fait en message');
            }
                // Création et persistance du message
            $message = $this->createMessage(null,$utilisateur, $courrier, $observation,null);
            $date = new DateTimeImmutable();
            $message->setIsReadAt($date);
            // Persistance des fichiers liés
            $this->fichiersService->persistFiles($files, $message);
            $historique= $this->historiquesService->updateHistoriqueNouvelleMessage($utilisateur, $courrier, $message,$numeroArrive);
            $this->save($historique);
            
            $message->setNumeroDestinataire($historique->getNumero());
            
            $this->save($message);
            
            $excludes = [ 'deletedAt','observation'];
            // $message->setDestinataire($destinataire);
            // $message->setExpediteur($expediteur);
            // $message->setCourrier($this->courriersService->cloneCourrier($courrier));
            $data = $message->toArray($excludes);
            $vueCourriers = $this->vueHistoriqueDetailService->getByHistoriqueId($historique->getId());
            if($vueCourriers == null){
             throw new Exception('Vue courrier non trouvée');
            }
            $data['courrier']= $this->vueHistoriqueDetailService->tranformerUtilisateur($vueCourriers,$excludes);
            $this->mercureService->sendNotification("message",$data);

            return $message;

        } catch (\Throwable $e) {
            throw $e;
        }
    }
    public function saveCourrierDto(Utilisateurs $utilisateur,CourriersDto $dto, ?array $files = []): Courriers
    {
        $this->em->getConnection()->beginTransaction();
        try {
            $courrier = $this->courriersService->saveDto($utilisateur, $dto);
            $this->envoyerNouvelleMessage($utilisateur,$courrier,$dto->getObservation(),$dto->getNumeroArrive() ,$files);
            $this->em->getConnection()->commit();
            return $courrier;
        } catch (\Throwable $th) {
            $this->em->getConnection()->rollBack();
            throw $th;
        }
    }

    public function sendNotificationMessage(Messages $messages,$excludes = []): void{
        $data = $messages->toArray($excludes);
        $vueCourriers = $this->vueHistoriqueDetailService->getByIdCourrier($messages->getCourrier()->getId());
        if(empty($vueCourriers)){
            throw new Exception('Vue courrier non trouvée');
        }
        $excludesCourrier = ['isSend','utilisateurId','dateMessage','cloturerPar','createdAt','expediteurId','destinataireId'];
        $data['courrier']= $this->vueHistoriqueDetailService->tranformerUtilisateur($vueCourriers[0],$excludesCourrier);
        $this->mercureService->sendNotification("lectureMessage",$data);
    }

    public function lireMessage(int $messageId,Utilisateurs $user, int $numeroArrivee): Messages
    {
        $this->em->getConnection()->beginTransaction();
        try {
            $message = $this->getValidatedMessage($messageId);
            if($message->getIsReadAt() !== null) {
                throw new Exception("Message déjà marqué comme lu");
            }
            if ($message->getDestinataire()->getId() !== $user->getId()) {
                throw new Exception("Vous n'êtes pas le destinataire de ce message");
            }
            $message->setIsReadAt(new DateTimeImmutable());
            $historiques = $this->historiquesService->modifierHistoriqueVoirMessage($user, $message, $numeroArrivee);
            $excludes = ['deletedAt','observation'];
            $message->setNumeroDestinataire($historiques[1]->getNumero());
            $this->sendNotificationMessage($message, $excludes);
            $this->em->getConnection()->commit();
            return $this->save($message);
        } catch (\Throwable $th) {
            $this->em->getConnection()->rollBack();
            throw $th;
        }
        
    }

    /**
     * Marque un message comme non lu (réinitialise isReadAt à null)
     */
    public function marquerNonLu(int $messageId, Utilisateurs $user): Messages
    {
        $message = $this->getValidatedMessage($messageId);
        if ($message->getDestinataire()->getId() !== $user->getId()) {
            return $message;
        }
        $message->setIsReadAt(null);
        $excludes = ['deletedAt','observation'];
        $this->sendNotificationMessage($message, $excludes);
        return $this->save($message);
   }

     
    
    /**
     * Récupère les messages associés à un courrier avec gestion des droits
     */
    public function getMessagesByCourrier(int $courrierId,OrderCriteria $orderCriteria,PaginationCriteria $paginationCriteria): array
    {
        $conditions = [
            new ConditionCriteria('courrier', $courrierId, '='),
            new ConditionCriteria('createdAt', $paginationCriteria->getValue(), '<'),
        ];

        $joins = [
            new JoinCriteria('m.destinataire', 'd', 'LEFT'),
            new JoinCriteria('m.expediteur', 'e', 'LEFT'),
        ];
        $messages = $this->search($conditions, $orderCriteria, $paginationCriteria, $joins);
        return $messages;
    }
    public function getAllMessagesByCourrier(int $courrierId,OrderCriteria $orderCriteria): array
    {
        $conditions = [
            new ConditionCriteria('courrier', $courrierId, '='),
        ];

        $joins = [
            new JoinCriteria('m.destinataire', 'd', 'LEFT'),
            new JoinCriteria('m.expediteur', 'e', 'LEFT'),
        ];

        $messages = $this->search($conditions, $orderCriteria, null, $joins);
        return $messages;
    }

    /**
     * Récupère le détail d'un message avec vérification des droits
     */
    public function getMessageDetail(int $messageId, int $userId): array
    {
        $message = $this->repo->getById($messageId);
        $this->validationService->throwIfNull($message, "Message avec l'ID $messageId introuvable.");

        // Sécurité : Seul l'expéditeur ou le destinataire peut voir les détails
        if (
            $message->getExpediteur()->getId() !== $userId &&
            $message->getDestinataire()->getId() !== $userId
        ) {
            throw new Exception("Vous n'êtes pas autorisé à consulter ce message.", 403);
        }

        return $message->toArray();
    }
    /**
     * Récupère un message par son ID
     */
    public function getById(int $id): ?Messages
    {
        return $this->repo->getById($id);
    }
    private function validerTranfers(Utilisateurs $precedentDestinataire,Utilisateurs $expediteur): void{
        if ($precedentDestinataire->getId()!= $expediteur->getId()) {
            throw new Exception("Seule le personne conserner peut partager son message.");
        }
    }
    private function validerDernierMessage(Messages $message): void{
        if ($message->getDateValidation() !== null) {
            throw new Exception("Seule le dernier message peut être transferé.");
        }
    }


    // Transferer un message à un autre utilisateur 
    public function transfererMessage(
        Messages $message,
        Utilisateurs $utilisateur,
        Utilisateurs $nouveauDestinataire,
        ?string $observation = null,
        ?string $bordureau = null,
        array $files = []
    ): Messages {
        $this->em->getConnection()->beginTransaction();

        try {
            // Validation du transfert
            $destinatairePrecedent = $message->getDestinataire();
            $this->validerTranfers($destinatairePrecedent, $utilisateur);
            $this->validerDernierMessage($message);

            // Envoi du nouveau message
            $nouveauMessage = $this->envoyerMessage(
                $utilisateur->getId(),
                $nouveauDestinataire->getId(),
                $message->getCourrier()->getId(),
                $observation,
                $bordureau,
                $files
            );
            // Mise à jour de la date de validation
            $date = new DateTimeImmutable();
            $message->setIsReadAt($message->getIsReadAt() ? $message->getIsReadAt() : $date);
            $message->setIsTraiterAt($date);
            $message->setDateValidation($date);
            $this->save($message);
            $this->save($nouveauMessage);
            $excludes = ['deletedAt','observation'];    
            $historiques= $this->historiquesService->tranformerMessageEnHistorique($nouveauMessage);

            if (count($historiques) < 2) {
                throw new Exception('Le message doit avoir au moins 2 historiques');
            }
            $nouveauMessage->setNumeroExpediteur($historiques[0]->getNumero());
            $this->save($nouveauMessage);
        
            $data = $nouveauMessage->toArray($excludes);
            $vueCourriers = $this->vueHistoriqueDetailService->getByHistoriqueId($historiques[1]->getId());
            if($vueCourriers == null){
                throw new Exception('Vue courrier non trouvée');
            }
            $data['courrier']= $this->vueHistoriqueDetailService->tranformerUtilisateur($vueCourriers,$excludes);
            $this->mercureService->sendNotification("message",$data);

            $this->sendNotificationMessage($message, $excludes);

            $this->em->getConnection()->commit();

            return $nouveauMessage;
        } catch (\Throwable $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }
    }
    public function transfererMessageById(
        int $messageId,
        int $expediteurId,
        int $nouveauDestinataireId,
        ?string $observation = null,
        ?string $bordureau,
        array $files = []
    ): Messages {
        // Récupération et validation du message
        $message = $this->getValidatedMessage($messageId);

        // Récupération et validation des utilisateurs
        $expediteur = $this->utilisateursService->getValidatedUser($expediteurId, 'Expéditeur');
        $nouveauDestinataire = $this->utilisateursService->getValidatedUser($nouveauDestinataireId, 'Nouveau destinataire');

        // Transfert du message
        return $this->transfererMessage($message, $expediteur, $nouveauDestinataire, $observation, $bordureau,$files);
    }
    public function envoyerEmailSuivre(string $reference)
    {
        $courrier = $this->courriersService->getByReference($reference);
        if ($courrier === null) {
            return;
        }
        $messages = $this->getAllMessagesByCourrier($courrier->getId(),new OrderCriteria());
        $this->courriersService->genererEmailSuiviMessage($courrier, $messages);
    }
    #[Override]
    public function transformerArray(array $entities, array $exclude = []): array
    {
        $items = [];
        for ($i=0; $i < count($entities) ; $i++) { 
            $entity = $entities[$i];
            $items[$i] = $entity->toArray($exclude);
            // echo($entity->getCourrier()->getId());
            // $vueCourrier = $this->vueHistoriqueDetailService->getVerifierById($entity->getCourrier()->getId());
            // $items[$i]['courrier']= $this->vueHistoriqueDetailService->tranformerUtilisateur($vueCourrier,$exclude);
        }
        return $items;
    }
    public function getDernierMessageByCourrier(int $courrierId): ?Messages
    {
        return $this->getAllMessagesByCourrier($courrierId, new OrderCriteria())[0]??null; 
    }
    public function traiterMessage(int $courrierId,DateTimeImmutable $date): ?Messages
    {
        $dernierMessage = $this->getDernierMessageByCourrier($courrierId);
        if($dernierMessage== null)
        {
            throw new Exception("Le message du courrierId $courrierId n'existe pas.");
        }
        if ($dernierMessage->getIsReadAt() === null) {
            $dernierMessage->setIsReadAt($date);
        }
        $dernierMessage->setIsTraiterAt($date);
        return $this->save($dernierMessage);
        
    }
    public function cloturerCourrier(int $id,Utilisateurs $user): object
    {
         $conn = $this->em->getConnection();
        $conn->beginTransaction(); // Début de la transaction
        try {
            $courrier = $this->courriersService->getValidatedCourrier($id);
            $date = new DateTimeImmutable();
            $courrier->setDateValidation($date);
            $courrier->setCloturePar($user);
            $this->courriersService->envoyerMailCloturer($courrier, $user);
            $excludes = ['deletedAt','observation'];
            $data = $courrier->toArray($excludes);

            $this->mercureService->sendNotification("clotureCourrier",$data);
            
            $valiny= $this->courriersService->save($courrier);
            $messages = $this->traiterMessage($id, $date);
            $this->sendNotificationMessage($messages, $excludes);

            $conn->commit(); // Commit de la transaction
            return $valiny;
          
        } catch (\Throwable $th) {
            $conn->rollBack(); // Rollback de la transaction
            throw $th;
        }

    }
    public function recupererMessageExterne(
        Messages $message,
        Utilisateurs $utisateurExterne,
        Utilisateurs $nouveauDestinataire,
        ?string $observation = null,
        array $files = []
    ): Messages {
        $expediteurPrecedent = $message->getExpediteur();
        $this->validerTranfers($expediteurPrecedent, $nouveauDestinataire);
        return $this->transfererMessage($message, $utisateurExterne, $nouveauDestinataire, $observation, null, $files);

    }
    public function recupererMessageExterneById(
        int $messageId,
        int $nouveauDestinataireId,
        ?string $observation = null,
        array $files = []
    ): Messages {
        // Récupération et validation du message
        $message = $this->getValidatedMessage($messageId);
        $utisateurExterneId = 2;

        // Récupération et validation des utilisateurs
        $utisateurExterne = $this->utilisateursService->getValidatedUser($utisateurExterneId, 'Utilisateur externe');
        $nouveauDestinataire = $this->utilisateursService->getValidatedUser($nouveauDestinataireId, 'Nouveau destinataire');

        // Transfert du message
        return $this->recupererMessageExterne($message, $utisateurExterne, $nouveauDestinataire, $observation, $files);
    }
    
}
