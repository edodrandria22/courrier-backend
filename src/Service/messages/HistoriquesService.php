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
    public function getNbCourrierByUser(Utilisateurs $utilisateurs, bool $isSend): int
    {
        $numeroCourrier = $this->numeroCourriersService->getByUtilisateurId($utilisateurs->getId(),$isSend, date('Y'));
        $numeroDepart = ($numeroCourrier[0] ?? null)?->getNumero() ?? 0;
        $valiny = $this->repo->getNbCourrierByUser($utilisateurs,$isSend);
        
        return $valiny + $numeroDepart;

    }
    private function updateHistorique(Utilisateurs $utilisateur, Courriers $courrier, bool $isSend,Messages $messages): Historiques
    {
        // Créer nouveau historique
        $nbCourriers = $this->getNbCourrierByUser($utilisateur,$isSend) +1;
        $historique = new Historiques();
        $historique->setUtilisateur($utilisateur);
        $historique->setCourrier($courrier);
        $historique->setIsSend($isSend);
        $historique->setMessage($messages);
        $historique->setNumero($nbCourriers);
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

        $historiqueExpditeur->setNumRef($historiqueDestinataire->getNumero());
        $historiqueExpditeur->setObservation($messages->getObservation());

        $historiqueDestinataire->setNumRef($historiqueExpditeur->getNumero());

        $this->save($historiqueExpditeur);
        $this->save($historiqueDestinataire);
        
        return [$historiqueExpditeur, $historiqueDestinataire];
    }
    public function getByMessageIdAndIsSend(Historiques $historiques): ?Historiques
    {
        $conditions = [
            new ConditionCriteria('message', $historiques->getMessage()->getId(), '='),
            new ConditionCriteria('isSend', !$historiques->getIsSend(), '='),
        ];
        return $this->search($conditions,new OrderCriteria('createdAt', 'desc'))[0] ?? null;
    }
    public function verifierUtilisateur(Utilisateurs $utilisateur, Utilisateurs $utilisateurExpditeur){
        if($utilisateur->getId() !== $utilisateurExpditeur->getId()){
            throw new \Exception("Vous n'êtes pas autorisé à modifier cette observation");
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

            $historiqueReception = $this->getByMessageIdAndIsSend($historique);

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

            $this->verifierUtilisateur($utilisateur, $historique->getUtilisateur());

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

    
    
}
