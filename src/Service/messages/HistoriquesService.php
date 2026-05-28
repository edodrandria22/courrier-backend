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
use App\Service\utils\BaseService;
use App\Service\utils\ValidationService;
use Doctrine\ORM\EntityManagerInterface;

class HistoriquesService extends BaseService
{
    public function __construct(
        private readonly HistoriquesRepository $repo,
        EntityManagerInterface $em,
        private readonly ValidationService $validationService
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
    public function getHistoriques(Utilisateurs $user, OrderCriteria $orderCriteria,PaginationCriteria $paginationCriteria): array
    {
        $conditions = [
            new ConditionCriteria('utilisateur', $user->getId(), '='),
            new ConditionCriteria('createdAt', $paginationCriteria->getValue(), '<'),
        ];
        return $this->search($conditions, $orderCriteria, $paginationCriteria);
    }
    private function updateHistorique(Utilisateurs $utilisateur, Courriers $courrier, bool $isSend): void
    {
        // Supprimer ancien historique
        $dernierHistorique = $this->getByUserAndCourrier($utilisateur, $courrier);
        $this->delete($dernierHistorique);

        // Créer nouveau historique
        $historique = new Historiques();
        $historique->setUtilisateur($utilisateur);
        $historique->setCourrier($courrier);
        $historique->setIsSend($isSend);

        $this->save($historique);
    }
    public function tranformerMessageEnHistorique(Messages $messages): void
    {
        $courrier = $messages->getCourrier();
      
        #Ajouter une nouvelle historique pour l'expediteur
        $this->updateHistorique(
            $messages->getExpediteur(),
            $courrier,
            true
        );

        #Ajouter une nouvelle historique pour le destinataire
        $this->updateHistorique(
            $messages->getDestinataire(),
            $courrier,
            false
        );
    }

    
}
