<?php

namespace App\Service\messages;

use App\Dto\utils\ConditionCriteria;
use App\Dto\utils\OrderCriteria;
use App\Dto\utils\PaginationCriteria;
use App\Entity\courriers\Courriers;
use App\Entity\messages\Historiques;
use App\Entity\messages\Messages;
use App\Entity\utilisateurs\Utilisateurs;
use App\Repository\messages\HistoriquesRepository;
use App\Service\courriers\NumeroCourriersService;
use App\Service\utils\BaseService;
use Doctrine\ORM\EntityManagerInterface;

class HistoriquesService extends BaseService
{
    public function __construct(
        private readonly HistoriquesRepository $repo,
        private readonly NumeroCourriersService $numeroCourriersService,
        EntityManagerInterface $em
    ) {
        parent::__construct($em);
    }  
    public function getRepository()
    {
        return $this->repo;
    }
    public function getByUserAndCourrier(Utilisateurs $user,Courriers $courrier): ?Historiques
    {
        $conditions = [
            new ConditionCriteria('utilisateur', $user->getId(), '='),
            new ConditionCriteria('courrier', $courrier->getId(), '='),
        ];
        return $this->search($conditions)[0] ?? null;
    }
    public function getHistoriques(Utilisateurs $user, OrderCriteria $orderCriteria,PaginationCriteria $paginationCriteria,bool $isSend): array
    {
        $conditions = [
            new ConditionCriteria('utilisateur', $user->getId(), '='),
            new ConditionCriteria('createdAt', $paginationCriteria->getValue(), '<'),
            new ConditionCriteria('isSend', $isSend, '='),
        ];
        return $this->search($conditions, $orderCriteria, $paginationCriteria);
    }
    public function getNbCourrierByUser(Utilisateurs $utilisateur, bool $isSend): int
    {
        $numeroCourrier = $this->numeroCourriersService->getNumeroDepartActuel($utilisateur,$isSend, date('Y'));
        $numeroDepart = $numeroCourrier->getNumero();
        $valiny = $this->repo->getNbCourrierByUser($utilisateur,$isSend);
        
        return $valiny + $numeroDepart;

    }
    public function updateHistoriqueNouvelleMessage(Utilisateurs $utilisateur, Courriers $courrier, bool $isSend,Messages $messages): Historiques
    {
        // Créer nouveau historique
        $historique = new Historiques();
        $historique->setUtilisateur($utilisateur);
        $historique->setCourrier($courrier);
        $historique->setIsSend($isSend);
        $historique->setMessage($messages);
        $historique->setObservation($messages->getObservation());
        if($isSend  == false){
            $nbCourriers = $this->getNbCourrierByUser($utilisateur,$isSend) +1;
            $historique->setNumero($nbCourriers);
        }
        return $historique;
    }
    public function updateHistorique(Utilisateurs $utilisateur, Courriers $courrier, bool $isSend,Messages $messages): Historiques
    {
        // Créer nouveau historique
        $historique = new Historiques();
        $historique->setUtilisateur($utilisateur);
        $historique->setCourrier($courrier);
        $historique->setIsSend($isSend);
        $historique->setMessage($messages);
        if($isSend  == true){
            $nbCourriers = $this->getNbCourrierByUser($utilisateur,$isSend) +1;
            $historique->setNumero($nbCourriers);
        }
        return $historique;
    }
    public function tranformerMessageEnHistorique(Messages $messages): array
    {
        $courrier = $messages->getCourrier();
      
        #Ajouter une nouvelle historique pour l'expediteur
        $historiqueExpditeur = $this->updateHistorique(
            $messages->getExpediteur(),
            $courrier,
            true,
            $messages
        );

        #Ajouter une nouvelle historique pour le destinataire
        $historiqueDestinataire = $this->updateHistorique(
            $messages->getDestinataire(),
            $courrier,
            false,
            $messages
        );

        // $historiqueExpditeur->setNumRef($historiqueDestinataire->getNumero());
        $historiqueExpditeur->setObservation($messages->getObservation());

        $historiqueDestinataire->setNumRef($historiqueExpditeur->getNumero());

        $this->save($historiqueExpditeur);
        $this->save($historiqueDestinataire);
        
        return [$historiqueExpditeur, $historiqueDestinataire];
    }
    public function getByMessageAndIsSend(Messages $message, bool $isSend): ?Historiques
    {
        $conditions = [
            new ConditionCriteria('message', $message->getId(), '='),
            new ConditionCriteria('isSend', $isSend, '='),
        ];
        return $this->search($conditions,new OrderCriteria('createdAt', 'desc'))[0] ?? null;
    }
    public function verifierUtilisateur(Utilisateurs $utilisateur, Utilisateurs $utilisateurExpditeur,String $message){
        if($utilisateur->getId() !== $utilisateurExpditeur->getId()){
            throw new \Exception("Vous n'êtes pas autorisé à ".$message);
        }
    }
    /**
     * Met à jour la date de réception de l'historique et de son historique associé
     */
    private function mettreAJourDateReception(Historiques $historique): void
    {
        if ($historique->getIsSend() === false && $historique->getDateReception() === null) {
            $dateReception = new \DateTimeImmutable();

            $historique->setDateReception($dateReception);

            $historiqueReception = $this->getByMessageAndIsSend($historique->getMessage(),$historique->getIsSend());

            if ($historiqueReception !== null) {
                $historiqueReception->setDateReception($dateReception);
                $this->save($historiqueReception);
            }
        }
    }

    public function modifierObservation(
        int $idHistorique,
        Utilisateurs $utilisateur,
        ?string $observation
    ): Historiques {
        $this->em->beginTransaction();

        try {
            $historique = $this->getVerifierById($idHistorique);

            $this->verifierUtilisateur($utilisateur, $historique->getUtilisateur(), "modifier l'observation");

            $this->mettreAJourDateReception($historique);

            $historique->setObservation($observation);

            $this->save($historique);

            $this->em->commit();

            return $historique;

        } catch (\Throwable $e) {
            $this->em->rollback();

            throw $e;
        }
    }
    public function modifierHistoriqueVoirMessage(Utilisateurs $utilisateur,Messages $message): array
    {
        $this->verifierUtilisateur($utilisateur, $message->getDestinataire(), "voir le message");
        $numeroArrivee = $this->getNbCourrierByUser($utilisateur,false) +1;
        $historiqueRecepteur = $this->getByMessageAndIsSend($message, false);
        $historiqueRecepteur->setNumero($numeroArrivee);
        $historiqueExpediteur = $this->getByMessageAndIsSend($message, true);
        $historiqueExpediteur->setNumRef($numeroArrivee);
        $this->save($historiqueRecepteur);
        $this->save($historiqueExpediteur);
        return [$historiqueExpediteur, $historiqueRecepteur];
    }
    

    
    
}
