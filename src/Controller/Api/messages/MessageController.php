<?php

namespace App\Controller\Api\messages;

use App\Controller\Api\utils\BaseApiController;
use App\Service\messages\MessagesService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Annotation\TokenRequired;

#[Route('/messages')]
class MessageController extends BaseApiController
{
    public function __construct(
        private readonly MessagesService $messagesService
    ) {
        
    }
    // #[Route('', name: 'api_messages_envoyer', methods: ['POST'])]
    // #[TokenRequired]
    // public function envoyer(Request $request): JsonResponse
    // {
    //     try {
    //         $user = $this->getUserFromRequest($request);

    //         // On supporte maintenant multipart/form-data pour les fichiers
    //         $data = $request->request->all();
    //         $uploadedFiles = $request->files->get('fichiers', []);
            
    //         $this->validatorService->validateRequiredFields($data, ['destId', 'courrierId']);

    //         $message = $this->messagesService->envoyerNouvelleMessage(
    //             expId: $user->getId(),
    //             destId: (int) $data['destId'],
    //             courrierId: (int) $data['courrierId'],
    //             observation: $data['observation'] ?? null,
    //             files: is_array($uploadedFiles) ? $uploadedFiles : [$uploadedFiles]
    //         );
    //         $excludes = ['createdAt', 'deletedAt'];
    //         $data = $message->toArray($excludes);
    //         return $this->jsonSuccess($data);
    //     } catch (\Throwable $e) {
    //         return $this->jsonError($e->getMessage(), $e->getCode() ?: 400);
    //     }
    // }
    #[Route('/transferer', name: 'api_messages_transferer', methods: ['POST'])]
    #[TokenRequired]
    public function transferer(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);

            // On supporte maintenant multipart/form-data pour les fichiers
            $data = $request->request->all();
            $uploadedFiles = $request->files->get('fichiers', []);

            $this->validatorService->validateRequiredFields($data, ['id', 'destId']);

            $message = $this->messagesService->transfererMessageById(
                messageId: (int) $data['id'],
                expediteurId: $user->getId(),
                nouveauDestinataireId: (int) $data['destId'],
                observation: $data['observation'] ?? null,
                bordureau: $data['bordureau'] ?? null,
                files: is_array($uploadedFiles) ? $uploadedFiles : [$uploadedFiles]
            );
            $excludes = ['createdAt', 'deletedAt'];
            $data = $message->toArray($excludes);
            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    /**
     * Marque un message comme lu
     */
    #[Route('/{id}/lire', name: 'api_messages_lire', methods: ['PATCH'], requirements: ['id' => '\d+'])]
    #[TokenRequired]
    public function lire(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $data = $request->toArray();
            $this->validatorService->validateRequiredFields($data, ['numeroArrivee']);
            
            $numeroArrivee = $data['numeroArrivee'];
            $message = $this->messagesService->lireMessage($id, $user, $numeroArrivee);
            $excludes = ['createdAt', 'deletedAt'];
            $data = $message->toArray($excludes);
            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    /**
     * Marque un message comme non lu (réinitialise isReadAt à null)
     */
    // #[Route('/{id}/non-lu', name: 'api_messages_non_lu', methods: ['PATCH'], requirements: ['id' => '\d+'])]
    // #[TokenRequired]
    // public function nonLu(int $id, Request $request): JsonResponse
    // {
    //     try {
    //         $user = $this->getUserFromRequest($request);
    //         $message = $this->messagesService->marquerNonLu($id, $user);
    //         $excludes = ['createdAt', 'deletedAt'];
    //         $data = $message->toArray($excludes);
    //         return $this->jsonSuccess($data);
    //     } catch (\Throwable $e) {
    //         return $this->jsonError($e->getMessage(), $e->getCode() ?: 400);
    //     }
    // }

    /**
     * Récupère le détail d'un message
     */
    #[Route('/{id}', name: 'api_messages_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    // #[TokenRequired]
    public function show(int $id): JsonResponse
    {
        try {
            $message = $this->messagesService->getById($id);
            $data = $message->toArray();
            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    #[Route('/recupererExterne', name: 'api_messages_recuperer_externe', methods: ['POST'])]
    #[TokenRequired]
    public function recupererExterne(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);

            // On supporte maintenant multipart/form-data pour les fichiers
            $data = $request->request->all();
            $uploadedFiles = $request->files->get('fichiers', []);

            $this->validatorService->validateRequiredFields($data, ['id']);

            $message = $this->messagesService->recupererMessageExterneById(
                messageId: (int) $data['id'],
                nouveauDestinataireId: $user->getId(),
                observation: $data['observation'] ?? null,
                files: is_array($uploadedFiles) ? $uploadedFiles : [$uploadedFiles]
            );
            $excludes = ['createdAt', 'deletedAt'];
            $data = $message->toArray($excludes);
            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), $e->getCode() ?: 400);
        }
    }

}
