<?php

namespace App\Controller\Api\courriers;

use App\Controller\Api\utils\BaseApiController;
use App\Dto\courriers\CourriersDto;
use App\Dto\courriers\RechercheCourriersDto;
use App\Dto\utils\OrderCriteria;
use App\Dto\utils\PaginationCriteria;
use App\Service\courriers\CourriersService;
use App\Service\courriers\VueHistoriqueDetailsService;
use App\Service\messages\MessagesService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Annotation\TokenRequired;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\HttpFoundation\Response;

#[Route('/courriers')]
class CourrierController extends BaseApiController
{
    public function __construct(
        private readonly CourriersService $courriersService,
        private readonly MessagesService $messagesService,
        private readonly VueHistoriqueDetailsService $vueHistoriqueDetailsService
    ) {
        
    }

    #[Route('', name: 'api_courriers_list', methods: ['GET'])]
    #[TokenRequired(['Utilisateur'])]
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $dateParam = $request->query->get('date');
            $referenceParam = $request->query->get('reference') ?? '';
            $date = $dateParam ? new DateTimeImmutable($dateParam) : new DateTimeImmutable();
            $limitParam = $request->query->get('limit');
            $limit = $limitParam ? (int)$limitParam : ($_ENV['LIMIT_PAGINATIONS'] ?? 10);
            $data = $this->courriersService->getAllCourrierByUserDateJson($user, $referenceParam, $date, $limit);
            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }

    /**
     * Liste tous les courriers impliquant l'utilisateur connecté (créateur, expéditeur ou destinataire)
     */
    #[Route('/getAllbyUser', name: 'api_courriers_get_all_by_user', methods: ['GET'])]
    #[TokenRequired(['Utilisateur'])]
    public function getAllbyUser(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);

            $dateParam = $request->query->get('date');
            $value = $request->query->get('isTraiterAt');
            $valueRecu = $request->query->get('isRecu');

            $isTraiterAt = $this->validatorService->toNullableBool($value);
            
            $isRecu = $this->validatorService->toNullableBool($valueRecu);
            $date = $dateParam ? new DateTimeImmutable($dateParam) : new DateTimeImmutable();
            $limitParam = $request->query->get('limit');
            $limit = $limitParam ? (int)$limitParam : ($_ENV['LIMIT_PAGINATIONS'] ?? 10);
            $paginationCriteria = new PaginationCriteria($date, $limit);
            $orderCriteria = new OrderCriteria();
            $orderCriteria->setField("dateMessage");
            $courriers = $this->courriersService->getAllByUserJson($user, $orderCriteria, $paginationCriteria,false,$isTraiterAt,$isRecu);
            
            return $this->jsonSuccess($courriers);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }
    #[Route('/getAllbyUserSend', name: 'api_courriers_get_all_by_user_recu', methods: ['GET'])]
    #[TokenRequired(['Utilisateur'])]
    public function getAllbyUserRecu(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);

            $dateParam = $request->query->get('date');
            $date = $dateParam ? new DateTimeImmutable($dateParam) : new DateTimeImmutable();
            $limitParam = $request->query->get('limit');
            $limit = $limitParam ? (int)$limitParam : ($_ENV['LIMIT_PAGINATIONS'] ?? 10);
            $paginationCriteria = new PaginationCriteria($date, $limit);
            $orderCriteria = new OrderCriteria();
            $orderCriteria->setField("dateMessage");
            $courriers = $this->courriersService->getAllByUserJson($user, $orderCriteria, $paginationCriteria,true);
            
            return $this->jsonSuccess($courriers);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }


    #[Route('', name: 'api_courriers_creer', methods: ['POST'])]
    #[TokenRequired(['Utilisateur'])]
    public function creer(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $dto = $this->deserializeFormDataAndValidate(
                $request,
                CourriersDto::class
            );
            $uploadedFiles = $request->files->get('fichiers', []);
            $message = $this->messagesService->saveCourrierDto($user, $dto , files: is_array($uploadedFiles) ? $uploadedFiles : [$uploadedFiles]);
            $excludes = ['deletedAt'];
            $data = $message->toArray($excludes);
            return $this->jsonSuccess($data);

        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }
    #[Route('/{id}', name: 'api_courriers_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    #[TokenRequired(['Utilisateur'])]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $dto = $this->deserializeAndValidate(
                $request,
                CourriersDto::class
            );
            $courrier = $this->courriersService->updateDtoId($user, $id, $dto);
            $excludes = ['deletedAt','dateValidation','cloturerPar'];
            $data = $courrier->toArray($excludes);
            return $this->jsonSuccess($data);

        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }

    #[Route('/{id}', name: 'api_courriers_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    #[TokenRequired]
    public function show(int $id): JsonResponse
    {
        try {
            $courrier = $this->courriersService->getValidatedCourrier($id);
            $excludes = ['deletedAt','dateValidation','cloturerPar'];
            $data = $courrier->toArray($excludes);
            return $this->jsonSuccess($data);

        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    #[Route('/{id}/messages', name: 'api_courriers_messages', methods: ['GET'], requirements: ['id' => '\d+'])]
    #[TokenRequired]
    public function messages(int $id, Request $request): JsonResponse
    {
        try {
            $dateParam = $request->query->get('date');
            $date = $dateParam ? new DateTimeImmutable($dateParam) : new DateTimeImmutable();
            $limitParam = $request->query->get('limit');
            $limit = $limitParam ? (int)$limitParam : ($_ENV['LIMIT_PAGINATIONS'] ?? 10);  
            $paginationCriteria = new PaginationCriteria($date, $limit);
            $orderCriteria = new OrderCriteria();
            $messages = $this->messagesService->getMessagesByCourrier($id, $orderCriteria, $paginationCriteria);
            $excludes = ['deletedAt','utilisateurId','destinataireId','expediteurId','observation'];
            $data = $this->messagesService->transformerArray($messages, $excludes);
            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            // throw $e;
            return $this->jsonError($e->getMessage(),  400);
        }
    }

    #[Route('/{id}/cloturer', name: 'api_courriers_cloturer', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[TokenRequired(['Utilisateur'])]
    public function cloturer(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $courrier = $this->messagesService->cloturerCourrier($id, $user);
            $data = $courrier->toArray();
            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }

    #[Route('/mercure', name: 'api_mercure', methods: ['POST'])]
    public function send(HubInterface $hub): Response
    {

        $data = [
            'isReadAt' => null,
            'numeroExpediteur' => 9,
            'numeroDestinataire' => null,
            'isTraiterAt' => null,
            'dateValidation' => null,
            'id' => 235,
            'createdAt' => '2026-07-29 16:50:36',

            'expediteur' => [
                'email' => 'jean@gmail.com',
                'nom' => 'JEAN',
                'prenom' => 'Paul',
                'adresse' => 'Analamahitsy Tanana',
                'id' => 94,
                'createdAt' => '2026-07-01 09:50:34',
                'idRole' => 2,
            ],

            'destinataire' => [
                'email' => 'njara@gmail.com',
                'nom' => 'NJARA',
                'prenom' => 'Hery',
                'adresse' => 'By Pass',
                'id' => 93,
                'createdAt' => '2026-06-16 09:31:48',
                'idRole' => 2,
            ],

            'fichiers' => [],

            'courrier' => [
                'historiqueId' => 490,
                'id' => 114,
                'utilisateurId' => 93,
                'destinataireId' => 93,
                'expediteurId' => 94,
                'messageId' => 235,

                'isReadAt' => null,
                'isTraiterAt' => null,
                'isSend' => false,

                'numero' => null,
                'numRef' => 9,
                'numeroExpediteur' => 9,
                'numeroDestinataire' => null,
                'dateReception' => null,

                'detailPersonnes' => [],

                'reference' => '29072026/REF56',
                'object' => 'dihdeuih',
                'description' => '',
                'dateMessage' => '2026-07-29 16:50:36',
                'cloturePar' => null,

                'isConfidentiel' => false,
                'dateValidation' => null,
                'createdAt' => '2026-07-29 16:50:25',
                'statut' => 'en_cours',

                'expediteur' => [
                    'email' => 'jean@gmail.com',
                    'nom' => 'JEAN',
                    'prenom' => 'Paul',
                    'adresse' => 'Analamahitsy Tanana',
                ],

                'destinataire' => [
                    'email' => 'njara@gmail.com',
                    'nom' => 'NJARA',
                    'prenom' => 'Hery',
                    'adresse' => 'By Pass',
                ],
            ],
        ];
        $lectureMessage = [
            'isReadAt' => '2026-07-30 10:01:57',
            'numeroExpediteur' => 43,
            'numeroDestinataire' => 11,
            'isTraiterAt' => null,
            'dateValidation' => null,
            'id' => 217,
            'createdAt' => '2026-07-25 09:12:18',

            'expediteur' => [
                'email' => 'njara@gmail.com',
                'nom' => 'NJARA',
                'prenom' => 'Hery',
                'adresse' => 'By Pass',
                'id' => 93,
                'createdAt' => '2026-06-16 09:31:48',
                'idRole' => 2,
            ],

            'destinataire' => [
                'email' => 'randriadode@gmail.com',
                'nom' => 'TEST',
                'prenom' => 'Test',
                'adresse' => 'Analamahitsy Tanana',
                'id' => 91,
                'createdAt' => '2026-06-11 11:53:25',
                'idRole' => 2,
            ],

            'fichiers' => [],

            'courrier' => [
                'historiqueId' => 460,
                'id' => 105,
                'messageId' => 217,
                'isReadAt' => '2026-07-30 10:01:57',
                'isTraiterAt' => null,
                'numero' => 11,
                'numRef' => 43,
                'numeroExpediteur' => 43,
                'numeroDestinataire' => 10,
                'observation' => null,
                'dateReception' => null,
                'detailPersonnes' => [],
                'reference' => '25072026/REF4',
                'object' => 'Demande de congé',
                'description' => 'Test stat',
                'cloturePar' => null,
                'isConfidentiel' => false,
                'dateValidation' => null,
                'deletedAt' => null,
                'statut' => 'en_cours',

                'expediteur' => [
                    'email' => 'njara@gmail.com',
                    'nom' => 'NJARA',
                    'prenom' => 'Hery',
                    'adresse' => 'By Pass',
                    'deletedAt' => null,
                ],

                'destinataire' => [
                    'email' => 'randriadode@gmail.com',
                    'nom' => 'TEST',
                    'prenom' => 'Test',
                    'adresse' => 'Analamahitsy Tanana',
                    'deletedAt' => null,
                ],
            ],
        ];
        $update = new Update(
            'lectureMessage',
            json_encode($lectureMessage, JSON_THROW_ON_ERROR)
        );

        $hub->publish($update);

        return new Response('Message envoyé');
    }
    
    #[Route('/recherche', name: 'api_courriers_recherche', methods: ['POST'])]
    #[TokenRequired]
    public function recherche(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $dto = $this->deserializeAndValidate(
                $request,
                RechercheCourriersDto::class
            );
            
            $date = $dto->date ?? new DateTimeImmutable();
            $limitParam = $request->query->get('limit');
            $limit = $limitParam ? (int)$limitParam : ($_ENV['LIMIT_PAGINATIONS'] ?? 10);
            $paginationCriteria = new PaginationCriteria($date, $limit);
            $orderCriteria = new OrderCriteria();
            $orderCriteria->addField(["historiqueId"]);
            $result = $this->vueHistoriqueDetailsService->searchByDto($user, $dto, $orderCriteria, $paginationCriteria);
            $excludes = ['deletedAt','mdp'];
            $data = $this->vueHistoriqueDetailsService->transformerArrayUtilisateur($result, $excludes);
            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }
    #[Route('/envoyer-email-suivre', name: 'api_courriers_envoyer_email_suivre', methods: ['POST'])]
    public function envoyerEmailSuivre(Request $request): JsonResponse
    {
        try {
            
            $data = json_decode($request->getContent(), true);

            $requiredFields = ['reference'];
            $this->validatorService->validateRequiredFields($data, $requiredFields);
            $reference = $data['reference'];
            $this->messagesService->envoyerEmailSuivre($reference);

            return $this->jsonSuccess(['reference' => $reference]);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }
    #[Route('/historique/{id}', name: 'api_courriers_historique', methods: ['PUT'])]
    #[TokenRequired(['Utilisateur'])]
    public function historique(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $body = $request->toArray();
            $observation = $body['observation'] ?? "";
            $vueHistoriqueDetail = $this->courriersService->modifierObservationHistorique($id, $user, $observation);
            $excludes = ['deletedAt'];
            return $this->jsonSuccess($vueHistoriqueDetail->toArray($excludes));
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }
    #[Route('/statistique', name: 'api_statistique', methods: ['GET'])]
    #[TokenRequired(['Utilisateur'])]
    public function statistique(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $dateDebutParam = $request->query->get('dateDebut');
            $dateFinParam = $request->query->get('dateFin');
            $dateDebut = $dateDebutParam ? new DateTimeImmutable($dateDebutParam) : new DateTimeImmutable();
            $dateFin = $dateFinParam ? new DateTimeImmutable($dateFinParam) : new DateTimeImmutable();
            
            $statistique = $this->courriersService->getStatistique($user, $dateDebut, $dateFin);
            
            return $this->jsonSuccess($statistique);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }
    #[Route('/nonTraite', name: 'api_nonTraite', methods: ['GET'])]
    #[TokenRequired(['Utilisateur'])]
    public function nonTraite(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $nonTraite = $this->courriersService->getNbCourrierNonTraite($user);
            $lu = $this->courriersService->getNbCourrierLu($user);
            $data = [
                'nonTraite' => $nonTraite,
                'nonLu' => $lu,
            ];
            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }

    

}
