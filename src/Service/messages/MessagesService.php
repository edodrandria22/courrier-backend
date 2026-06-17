<?php

namespace App\Service\messages;

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
        private readonly MercureService $mercureService
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
        Utilisateurs $expediteur,
        Utilisateurs $destinataire,
        Courriers $courrier,
        ?string $observation
    ): Messages {
        $message = new Messages();
        $message->setExpediteur($expediteur);
        $message->setDestinataire($destinataire);
        $message->setCourrier($courrier);
        $message->setObservation($observation);
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
        array $files = []
    ): Messages {
        $this->em->getConnection()->beginTransaction();

        try {
            // Récupération et validation des entités
            $expediteur = $this->utilisateursService->getValidatedUser($expId, "Expéditeur");
            $destinataire = $this->utilisateursService->getValidatedUser($destId, "Destinataire");
            $courrier = $this->courriersService->getValidatedCourrier($courrierId);
                // Création et persistance du message
            $message = $this->createMessage($expediteur, $destinataire, $courrier, $observation);
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
        int $expId,
        int $destId,
        int $courrierId,
        ?string $observation = null,
        array $files = []
    ): Messages {
        
        $this->em->getConnection()->beginTransaction();
        try {
            // Récupération et validation des entités
            $expediteur = $this->utilisateursService->getValidatedUser($expId, "Expéditeur");
            $destinataire = $this->utilisateursService->getValidatedUser($destId, "Destinataire");
            $courrier = $this->courriersService->getValidatedCourrier($courrierId);

            if ($courrier->getDateMessage()) {
                throw new Exception('Courrier deja fait en message');
            }
                // Création et persistance du message
            $message = $this->createMessage($expediteur, $destinataire, $courrier, $observation);
            // Persistance des fichiers liés
            $this->fichiersService->persistFiles($files, $message);
            $historiques= $this->historiquesService->tranformerMessageEnHistorique($message);

            if (count($historiques) < 2) {
                throw new Exception('Le message doit avoir au moins 2 historiques');
            }
            $message->setNumeroExpediteur($historiques[0]->getNumero());
            $message->setNumeroDestinataire($historiques[1]->getNumero());
            $this->save($message);
            
            $excludes = [ 'deletedAt','observation'];
            // $message->setDestinataire($destinataire);
            // $message->setExpediteur($expediteur);
            // $message->setCourrier($this->courriersService->cloneCourrier($courrier));
            $data = $message->toArray($excludes);
            $vueCourriers = $this->vueHistoriqueDetailService->getByIdCourrier($message->getCourrier()->getId());
            if(empty($vueCourriers)){
             throw new Exception('Vue courrier non trouvée');
            }
            $data['courrier']= $this->vueHistoriqueDetailService->tranformerUtilisateur($vueCourriers[0],$excludes);
            $this->mercureService->sendNotification("message",$data);

            $this->em->getConnection()->commit();

            return $message;

        } catch (\Throwable $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }
    }



    public function lireMessage(int $messageId,Utilisateurs $user): Messages
    {
        $message = $this->getValidatedMessage($messageId);
        if ($message->getDestinataire()->getId() !== $user->getId()) {
            return $message;
        }
        $message->setIsReadAt(new DateTimeImmutable());
        $excludes = ['deletedAt','observation'];
        $data = $message->toArray($excludes);
        $vueCourriers = $this->vueHistoriqueDetailService->getByIdCourrier($message->getCourrier()->getId());
            if(empty($vueCourriers)){
             throw new Exception('Vue courrier non trouvée');
            }
            $data['courrier']= $this->vueHistoriqueDetailService->tranformerUtilisateur($vueCourriers[0],$excludes);
        $this->mercureService->sendNotification("lectureMessage",$data);
        return $this->save($message);
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
        $data = $message->toArray($excludes);
        $vueCourriers = $this->vueHistoriqueDetailService->getByIdCourrier($message->getCourrier()->getId());
        if(empty($vueCourriers)){
            throw new Exception('Vue courrier non trouvée');
        }
        $excludesCourrier = ['isSend','utilisateurId','dateMessage','cloturerPar','createdAt','expediteurId','destinataireId'];
        $data['courrier']= $this->vueHistoriqueDetailService->tranformerUtilisateur($vueCourriers[0],$excludesCourrier);
        $this->mercureService->sendNotification("lectureMessage",$data);
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


    // Transferer un message à un autre utilisateur 
    public function transfererMessage(
        Messages $message,
        Utilisateurs $utilisateur,
        Utilisateurs $nouveauDestinataire,
        ?string $observation = null,
        array $files = []
    ): Messages {
        $this->em->getConnection()->beginTransaction();

        try {
            // Validation du transfert
            $destinatairePrecedent = $message->getDestinataire();
            $this->validerTranfers($destinatairePrecedent, $utilisateur);

            // Envoi du nouveau message
            $nouveauMessage = $this->envoyerMessage(
                $utilisateur->getId(),
                $nouveauDestinataire->getId(),
                $message->getCourrier()->getId(),
                $observation,
                $files
            );
            // Mise à jour de la date de validation
            $message->setDateValidation(new DateTimeImmutable());
            $this->save($message);
            $this->save($nouveauMessage);
            $excludes = ['deletedAt','observation'];    
            $historiques= $this->historiquesService->tranformerMessageEnHistorique($nouveauMessage);

            if (count($historiques) < 2) {
                throw new Exception('Le message doit avoir au moins 2 historiques');
            }
            $nouveauMessage->setNumeroExpediteur($historiques[0]->getNumero());
            $nouveauMessage->setNumeroDestinataire($historiques[1]->getNumero());
            $this->save($nouveauMessage);
        
            $data = $nouveauMessage->toArray($excludes);
            $vueCourriers = $this->vueHistoriqueDetailService->getByIdCourrier($nouveauMessage->getCourrier()->getId());
            if(empty($vueCourriers)){
                throw new Exception('Vue courrier non trouvée');
            }
            $data['courrier']= $this->vueHistoriqueDetailService->tranformerUtilisateur($vueCourriers[0],$excludes);
            $this->mercureService->sendNotification("message",$data);

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
        array $files = []
    ): Messages {
        // Récupération et validation du message
        $message = $this->getValidatedMessage($messageId);

        // Récupération et validation des utilisateurs
        $expediteur = $this->utilisateursService->getValidatedUser($expediteurId, 'Expéditeur');
        $nouveauDestinataire = $this->utilisateursService->getValidatedUser($nouveauDestinataireId, 'Nouveau destinataire');

        // Transfert du message
        return $this->transfererMessage($message, $expediteur, $nouveauDestinataire, $observation, $files);
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
}
