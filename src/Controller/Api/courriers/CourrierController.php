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
    #[TokenRequired]
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $courriers = $this->courriersService->getAllCourierDisponible($user);
            $excludes = ['deletedAt',"dateValidation","cloturePar"];
            $data = $this->courriersService->transformerArray($courriers, $excludes);
            
            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }

    /**
     * Liste tous les courriers impliquant l'utilisateur connecté (créateur, expéditeur ou destinataire)
     */
    #[Route('/getAllbyUser', name: 'api_courriers_get_all_by_user', methods: ['GET'])]
    #[TokenRequired]
    public function getAllbyUser(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);

            $dateParam = $request->query->get('date');
            $date = $dateParam ? new DateTimeImmutable($dateParam) : new DateTimeImmutable();
            $limit = $_ENV['LIMIT_PAGINATIONS'] ?? 10;  
            $paginationCriteria = new PaginationCriteria($date, $limit);
            $orderCriteria = new OrderCriteria();
            $courriers = $this->courriersService->getAllByUserJson($user, $orderCriteria, $paginationCriteria,false);
            
            return $this->jsonSuccess($courriers);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }
    #[Route('/getAllbyUserSend', name: 'api_courriers_get_all_by_user_recu', methods: ['GET'])]
    #[TokenRequired]
    public function getAllbyUserRecu(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);

            $dateParam = $request->query->get('date');
            $date = $dateParam ? new DateTimeImmutable($dateParam) : new DateTimeImmutable();
            $limit = $_ENV['LIMIT_PAGINATIONS'] ?? 10;  
            $paginationCriteria = new PaginationCriteria($date, $limit);
            $orderCriteria = new OrderCriteria();
            $courriers = $this->courriersService->getAllByUserJson($user, $orderCriteria, $paginationCriteria,true);
            
            return $this->jsonSuccess($courriers);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }


    #[Route('', name: 'api_courriers_creer', methods: ['POST'])]
    #[TokenRequired]
    public function creer(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $dto = $this->deserializeAndValidate(
                $request,
                CourriersDto::class
            );
            $courrier = $this->courriersService->saveDto($user, $dto);
            $excludes = ['deletedAt','dateValidation','cloturerPar'];
            $data = $courrier->toArray($excludes);
            return $this->jsonSuccess($data);

        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }
    #[Route('/{id}', name: 'api_courriers_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    #[TokenRequired]
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
            $limit = $_ENV['LIMIT_PAGINATIONS'] ?? 10;  
            $paginationCriteria = new PaginationCriteria($date, $limit);
            $orderCriteria = new OrderCriteria();
            $messages = $this->messagesService->getMessagesByCourrier($id, $orderCriteria, $paginationCriteria);
            $excludes = ['deletedAt','utilisateurId','destinataireId','expediteurId'];
            $data = $this->messagesService->transformerArray($messages, $excludes);
            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            // throw $e;
            return $this->jsonError($e->getMessage(),  400);
        }
    }

    #[Route('/{id}/cloturer', name: 'api_courriers_cloturer', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[TokenRequired]
    public function cloturer(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $courrier = $this->courriersService->cloturerCourrier($id, $user);
            $data = $courrier->toArray();
            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }

     #[Route('/mercure', name: 'api_mercure', methods: ['POST'])]
    public function send(HubInterface $hub): Response
    {
        $update = new Update(
            'message',
            json_encode([
                'message' => 'Bonjour depuis Symfony'
            ])
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
            $limit = $_ENV['LIMIT_PAGINATIONS'] ?? 10;  
            $paginationCriteria = new PaginationCriteria($date, $limit);
            $orderCriteria = new OrderCriteria();
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
    

}
