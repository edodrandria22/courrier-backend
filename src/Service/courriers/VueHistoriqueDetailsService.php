<?php

namespace App\Service\courriers;

use App\Dto\courriers\RechercheCourriersDto;
use App\Dto\utils\ConditionCriteria;
use App\Dto\utils\OrderCriteria;
use App\Dto\utils\PaginationCriteria;
use App\Entity\courriers\VueHistoriqueDetailPersonnes;
use App\Entity\courriers\VueHistoriqueDetails;
use App\Entity\utilisateurs\Utilisateurs;
use App\Repository\courriers\VueHistoriqueDetailsRepository;
use App\Service\utils\BaseService;
use App\Service\utilisateurs\UtilisateursService;
use Doctrine\ORM\EntityManagerInterface;

class VueHistoriqueDetailsService extends BaseService
{
    public function __construct(
        private readonly VueHistoriqueDetailsRepository $repo,
        private readonly UtilisateursService $utilisateurService,
        private readonly DetailPersonnesService $detailPersonnesService,
        private readonly VueHistoriqueDetailPersonnesService $vueHistoriqueDetailPersonnesService,
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
    

    private function notEmpty(?string $value): bool
    {
        return $value !== null && trim($value) !== '';
    }
    public function searchByDto(Utilisateurs $utilisateur, RechercheCourriersDto $dto, OrderCriteria $orderCriteria, PaginationCriteria $paginationCriteria): array
    {
        $data = $this->vueHistoriqueDetailPersonnesService->searchByDto($utilisateur, $dto, $orderCriteria, $paginationCriteria);
        foreach ($data as $historique) {
            $historique->setDetailPersonnes(
                $this->detailPersonnesService->getByCourrierId($historique->getId())
            );
        }

        return $data;
    }
    public function getHistoriques(
        Utilisateurs $user,
        OrderCriteria $orderCriteria,
        PaginationCriteria $paginationCriteria,
        bool $isSend,
        ?bool $isTraiterAt = null
    ): array {
        $conditions = [
            new ConditionCriteria('utilisateurId', $user->getId(), '='),
            new ConditionCriteria('dateMessage', $paginationCriteria->getValue(), '<'),
            new ConditionCriteria('isSend', $isSend, '='),
        ];

        if ($isTraiterAt !== null) {
            $conditions[] = new ConditionCriteria(
                'isTraiterAt',
                null,
                $isTraiterAt ? 'IS NOT NULL' : 'IS NULL'
            );
        }

        $historiques = $this->search($conditions, $orderCriteria, $paginationCriteria);

        foreach ($historiques as $historique) {
            $historique->setDetailPersonnes(
                $this->detailPersonnesService->getByCourrierId($historique->getId())
            );
        }

        return $historiques;
    }
    public function tranformerUtilisateur(VueHistoriqueDetails | VueHistoriqueDetailPersonnes $entite,array $exclude = []): array
    {
        
        $valiny = $entite->toArray($exclude); 
        $excludeUtilisateur = array_merge($exclude, ['mdp','id','idRole','role','createdAt']);

        $expediteur = $this->utilisateurService->cloneUtilisateur($this->utilisateurService->getById($entite->getExpediteurId() ?: 0));
          
        $destinataire = $this->utilisateurService->cloneUtilisateur($this->utilisateurService->getById($entite->getDestinataireId() ?: 0));
            
        $valiny['expediteur'] = $expediteur?->toArray($excludeUtilisateur) ?? null;
        $valiny['destinataire'] = $destinataire?->toArray($excludeUtilisateur) ?? null;
        return $valiny;
    }
    
    public function transformerArrayUtilisateur(array $entities, array $exclude = []): array
    {
        $valiny = [];
        for ($i = 0; $i < count($entities); $i++) {
            $valiny[$i] = $this->tranformerUtilisateur($entities[$i],$exclude); 
        }
        return $valiny;
    }
    public function getByIdCourrier(int $id): array{
        $conditions = [
            new ConditionCriteria('id', $id, '='),
            new ConditionCriteria('isSend', false, '='),
        ];
        return $this->search($conditions,new OrderCriteria('dateMessage', 'desc'));
    }
    public function getByHistoriqueId(int $historiqueId): ?VueHistoriqueDetails{
        $conditions = [
            new ConditionCriteria('historiqueId', $historiqueId, '='),
        ];
        return $this->search($conditions,new OrderCriteria('dateMessage', 'desc'))[0] ?? null;
    }
    public function getNbCourrierParUtilisateur(Utilisateurs $utilisateur,array $conditions = []): int{
        $conditions = [
            new ConditionCriteria('utilisateurId', $utilisateur->getId(), '='),
            ...$conditions
        ];
        return $this->aggregate('count', 'historiqueId',$conditions);
    }
    public function getNbCourrierParUtilisateurEntreDates(Utilisateurs $utilisateur,\DateTimeInterface $dateDebut,\DateTimeInterface $dateFin,array $conditions): int{
        $conditions = [
            new ConditionCriteria('dateMessage', $dateDebut, '>='),
            new ConditionCriteria('dateMessage', $dateFin, '<='),
            ...$conditions
        ];
        return $this->getNbCourrierParUtilisateur($utilisateur, $conditions);
    }

    public function getNbCourrierIsSend(Utilisateurs $utilisateur,\DateTimeInterface $dateDebut,\DateTimeInterface $dateFin,bool $isSend = false): int{
        $conditions = [
          new ConditionCriteria('isSend', $isSend, '=')
        ];
        return $this->getNbCourrierParUtilisateurEntreDates($utilisateur, $dateDebut, $dateFin, $conditions);
    }
    public function getNbCourrierIsTraite(Utilisateurs $utilisateur,\DateTimeInterface $dateDebut,\DateTimeInterface $dateFin,bool $isTraite = false): int{
        $conditions = [
          new ConditionCriteria('isSend', false, '='),
          new ConditionCriteria(
                'isTraiterAt',
                null,
                $isTraite ? 'IS NOT NULL' : 'IS NULL'
          )
        ];
        return $this->getNbCourrierParUtilisateurEntreDates($utilisateur, $dateDebut, $dateFin, $conditions);
    }
    public function getNbCourrierIsRead(Utilisateurs $utilisateur,\DateTimeInterface $dateDebut,\DateTimeInterface $dateFin,bool $isRead = false): int{
        $conditions = [
          new ConditionCriteria('isSend', false, '='),
          new ConditionCriteria(
                'isReadAt',
                null,
                $isRead ? 'IS NOT NULL' : 'IS NULL'
          )
        ];
        return $this->getNbCourrierParUtilisateurEntreDates($utilisateur, $dateDebut, $dateFin, $conditions);
    }
    public function getStatistique(Utilisateurs $utilisateur,\DateTimeInterface $dateDebut,\DateTimeInterface $dateFin): array{
        $data = [
            'recu' => $this->getNbCourrierIsSend($utilisateur, $dateDebut, $dateFin, false),
            'envoye' => $this->getNbCourrierIsSend($utilisateur, $dateDebut, $dateFin, true),
            'traite' => $this->getNbCourrierIsTraite($utilisateur, $dateDebut, $dateFin, true),
            'nonTraite' => $this->getNbCourrierIsTraite($utilisateur, $dateDebut, $dateFin, false),
            'lu' => $this->getNbCourrierIsRead($utilisateur, $dateDebut, $dateFin, true),
            'nonLu' => $this->getNbCourrierIsRead($utilisateur, $dateDebut, $dateFin, false),
        ];
        return $data;
    }
    public function getNbCourrierNonTraite(Utilisateurs $utilisateurs) : int
    {
        $conditions = [
            new ConditionCriteria('isSend', false, '='),
            new ConditionCriteria('isTraiterAt', null, 'IS NULL'),
        ];
        return $this->getNbCourrierParUtilisateur($utilisateurs, $conditions);
    }


    
    
    
}