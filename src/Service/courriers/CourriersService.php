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
use App\Entity\courriers\DetailPersonnes;
use App\Entity\courriers\VueHistoriqueDetails;
use App\Service\messages\HistoriquesService;
use Exception;

class CourriersService extends BaseService
{
    public function __construct(
        private readonly CourriersRepository $repo,
        private readonly ValidationService $validator,
        EntityManagerInterface $entityManager,
        private readonly MailService $mailService,
        private readonly VueHistoriqueDetailsService $vueHistoriqueDetailsService,
        private readonly DetailPersonnesService $detailPersonnesService,
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
    
    public function genererListeDetailPersonne(CourriersDto $dto,Courriers $courrier): void
    {
        foreach ($dto->getDetailPersonnes() as $detailPersonne) {
            $nom = $detailPersonne->getName() ? mb_strtoupper($detailPersonne->getName()) : null;
            $prenom = $detailPersonne->getPrenom() ? mb_convert_case($detailPersonne->getPrenom(), MB_CASE_TITLE) : null;

            $detailPersonneEntity = new DetailPersonnes();
            $detailPersonneEntity->setCourrier($courrier);
            $detailPersonneEntity->setName($nom);
            $detailPersonneEntity->setPrenom($prenom);
            $detailPersonneEntity->setEmail($detailPersonne->getEmail());
            $detailPersonneEntity->setTelephone($detailPersonne->getTelephone());

            $messageCourrier = $this->genererMessageInsertionCourrier($detailPersonneEntity, $courrier);
            $this->mailService->sendEmail($detailPersonneEntity->getEmail(),"Référence de suivi de votre courrier au Mesupres" ,$messageCourrier);
            $courrier->addDetailPersonne($detailPersonneEntity);
        }
        
    }
    public function saveDto(Utilisateurs $utilisateur,CourriersDto $dto): Courriers
    {
        $this->em->getConnection()->beginTransaction();
        try {
            $courrier = new Courriers();
            $courrier->setReference($this->generateReference());
            $object = $dto->getIsConfidentiel() ? "Pli fermé" : $dto->getObject();
            $courrier->setObject($object);
            $courrier->setIsConfidentiel($dto->getIsConfidentiel());
            if(!$dto->getIsConfidentiel()){
                $courrier->setDescription($dto->getDescription());
            }
            $this->genererListeDetailPersonne($dto, $courrier);
            $courrier->setCreateur($utilisateur);
            $result = $this->save($courrier);
            $this->em->getConnection()->commit();
            return $result;
            
        } catch (Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }
    }
    public function updateDto(Utilisateurs $utilisateur,Courriers $courrier, CourriersDto $dto): Courriers
    {
        $this->em->getConnection()->beginTransaction();
        try {
            if($courrier->getCreateur()->getId() != $utilisateur->getId()){
                throw new Exception("Seule l'auteur du courrier peut le modifier son courrier");
            }
            // if ($courriers->getDateMessage()) {
            //     throw new Exception("Le courrier a déjà été envoyé, vous ne pouvez plus le modifier");
            // }
            $object = $dto->getIsConfidentiel() ? "Pli fermé" : $dto->getObject();
            $courrier->setObject($object);
            $courrier->setIsConfidentiel($dto->getIsConfidentiel());
            if(!$dto->getIsConfidentiel()){
                $courrier->setDescription($dto->getDescription());
            }
            $this->detailPersonnesService->deteteDetailPersonne($courrier->getId());
            $this->genererListeDetailPersonne($dto, $courrier);
            $result = $this->save($courrier);
            $this->em->getConnection()->commit();
            return $result;
        } catch (Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }
    }
    public function updateDtoId(Utilisateurs $utilisateur,int $id, CourriersDto $dto): Courriers
    {
        $courrier = $this->getVerifierById($id);
        return $this->updateDto($utilisateur, $courrier, $dto);
    }

   
    public function getAllCourierByUser(Utilisateurs $utilisateurs,String $reference,OrderCriteria $orderCriteria,PaginationCriteria $paginationCriteria): array
    {
        $conditions = [
            new ConditionCriteria('createur', $utilisateurs->getId(), '='),
            new ConditionCriteria('reference' , $reference, 'LIKE'),
            new ConditionCriteria($orderCriteria->getField()[0], $paginationCriteria->getValue(), '<'),

        ];
        return $this->search($conditions, $orderCriteria, $paginationCriteria);
    }
    public function getAllCourrierByUserDate(Utilisateurs $utilisateurs,String $reference, \DateTimeInterface $date, int $limit = 10): array
    {
        return $this->getAllCourierByUser($utilisateurs, $reference,new OrderCriteria('dateCreation','DESC'), new PaginationCriteria($date,$limit));
    }
    public function getAllCourrierByUserDateJson(Utilisateurs $utilisateurs,String $reference ,\DateTimeInterface $date, int $limit = 10): array
    {
        $courriers = $this->getAllCourrierByUserDate($utilisateurs,$reference, $date, $limit);
        $excludes = ['deletedAt',"dateValidation","cloturePar"];
        $data = [];
        for ($i = 0; $i < count($courriers); $i++) {
            $data[$i] = $courriers[$i]->toArray($excludes);
            $detailPersonnes = $this->detailPersonnesService->getByCourrierId($courriers[$i]->getId());
            $data[$i]['detailPersonnes'] = $this->detailPersonnesService->transformerArray($detailPersonnes, ['deletedAt','id']);
        }
        return $data;
    }
    public function getAllByUser(Utilisateurs $user,OrderCriteria $orderCriteria,PaginationCriteria $paginationCriteria,bool $isSend, ?bool $isTraiterAt = null,?bool $isRecu = null): array
    {
        $result = $this->vueHistoriqueDetailsService->getHistoriques($user, $orderCriteria,$paginationCriteria,$isSend,$isTraiterAt,$isRecu);
        return $result;
    }
    public function getAllByUserJson(Utilisateurs $user,OrderCriteria $orderCriteria,PaginationCriteria $paginationCriteria,bool $isSend,?bool $isTraiterAt = null,?bool $isRecu = null): array
    {
        if ($isTraiterAt) {
            $orderCriteria= new OrderCriteria('isTraiterAt','DESC');
        }
        // if($isRecu == true) {
        //     $orderCriteria= new OrderCriteria('isReadAt','DESC');
        // }
        $exclude = ['deletedAt','utilisateurId','destinataireId','expediteurId','dateReception'];
        $historique = $this->getAllByUser($user, $orderCriteria, $paginationCriteria, $isSend, $isTraiterAt, $isRecu);
        return $this->vueHistoriqueDetailsService->transformerArrayUtilisateur($historique, $exclude);
    }

    public function cloneCourrier(Courriers $courrierOriginal): Courriers
    {
        $clone = new Courriers();
        $clone->setId($courrierOriginal->getId());
        $clone->setReference($courrierOriginal->getReference()); // ou générer une nouvelle référence
        $clone->setObject($courrierOriginal->getObject());
        $clone->setDescription($courrierOriginal->getDescription());
        $clone->setDateMessage($courrierOriginal->getDateMessage());
        $clone->setCreateur($courrierOriginal->getCreateur());
        $clone->setCreatedAt($courrierOriginal->getCreatedAt());
        $clone->setCloturePar($courrierOriginal->getCloturePar()); // le clone n’est pas encore clôturé
        return $clone;
    }
    public function genererDivClorer(DetailPersonnes $detailPersonne,Utilisateurs $utilisateur,Courriers $courrier)
    {
        $nom = $detailPersonne->getName() . ' ' . $detailPersonne->getPrenom();
        $adresse = $utilisateur->getAdresse();
        $messageHtml = "Nous vous informons que votre courrier portant la référence <bolt>".$courrier->getReference()."</bolt> a été traité.<br> 
                        Object du courrier : <bolt>".$courrier->getObject()."</bolt><br> 
                        <p>Vous êtes invité à vous présenter aux coordonnées suivantes pour la suite de votre démarche :</p> 
                        <p><strong>Adresse :</strong> {$adresse}</p> 
                        <p>Merci de vous munir d’une pièce d’identité lors de votre passage.</p>";
        return $this->mailService->getHtmlMail($nom, $messageHtml);
    }
    public function envoyerMailCloturer(Courriers $courrier,Utilisateurs $utilisateur)
    {
        $subject = "Votre courrier au Mesupres n° ".$courrier->getReference()." est traité";
        $listeDetailsPersonnes = $courrier->getDetailPersonnes();
        foreach ($listeDetailsPersonnes as $detailPersonne) {
            $html = $this->genererDivClorer($detailPersonne, $utilisateur, $courrier);
            $this->mailService->sendEmail($detailPersonne->getEmail(), $subject, $html);
        }
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
    function genererEmailSuviDetailPersonne(DetailPersonnes $detailPersonne,String $listeDiv)
    {
        if ($detailPersonne->getEmail() === null) {
            return ;
        }
        $nom = $detailPersonne->getName() . ' ' . $detailPersonne->getPrenom();
            
        $message=$this->mailService->getHtmlMail($nom, $listeDiv);
        $this->mailService->sendEmail($detailPersonne->getEmail(), "Suivi du courier", $message);

    }
    public function genererEmailSuiviMessage(Courriers $courrier,array $listeMessage)
    {
        $listeDetailsPersonnes = $courrier->getDetailPersonnes();
        if (empty($listeDetailsPersonnes)) {
            return;
        }
        $listDiv = "";
        foreach ($listeMessage as $message) {
          $listDiv .= $message->getParticipantsHtml();  
        }
        
        foreach ($listeDetailsPersonnes as $detailPersonne) {
            $this->genererEmailSuviDetailPersonne($detailPersonne, $listDiv);
        }
    }
    public function modifierObservationHistorique(int $idHistorique,Utilisateurs $utilisateur ,?string $observation): ?VueHistoriqueDetails
    {
        $this->historiquesService->modifierObservation($idHistorique, $utilisateur, $observation);
        return $this->vueHistoriqueDetailsService->getByHistoriqueId($idHistorique);
    }
    public function genererMessageInsertionCourrier(DetailPersonnes $detailPersonne,Courriers $courrier)
    {
        $nom = $detailPersonne->getName() . ' ' . $detailPersonne->getPrenom();
        $objet = "Votre courrier ayant comme objet \"" . $courrier->getObject() . "\" a été bien enregistré.<b>";
        $messageHtml = "<bolt>Référence : " . $courrier->getReference() . " </bolt><br>"
            . "Vous pouvez suivre son traitement en ligne à l'adresse suivante : https://courrier.mesupres.mg";
        return $this->mailService->getHtmlMail($nom, $messageHtml);
    }
    public function getStatistique(
        Utilisateurs $utilisateur,
        \DateTimeImmutable $dateDebut,
        \DateTimeImmutable $dateFin
    ): array {
        $dateDebut = $dateDebut->setTime(0, 0, 0);
        $dateFin = $dateFin->setTime(23, 59, 59);

        return $this->vueHistoriqueDetailsService->getStatistique(
            $utilisateur,
            $dateDebut,
            $dateFin
        );
    }
    public function getNbCourrierNonTraite(Utilisateurs $utilisateurs) : int{
        return $this->vueHistoriqueDetailsService->getNbCourrierNonTraite($utilisateurs);
    }
    public function getNbCourrierLu(Utilisateurs $utilisateurs) : int{
        return $this->vueHistoriqueDetailsService->getNbCourrierLu($utilisateurs);
    }
  
    
  
    
}
