<?php

namespace App\Controller\Api\utilisateurs;


use App\Controller\Api\utils\BaseApiController;
use App\Dto\utilisateurs\UtilisateursDto;
use App\Dto\utils\OrderCriteria;
use App\Service\utilisateurs\UtilisateursService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Annotation\TokenRequired;
use App\Dto\utils\PaginationCriteria;
use App\Service\utilisateurs\VueUtilisateursService;
use DateTimeImmutable;

#[Route('/utilisateurs')]
class UtilisateurController extends BaseApiController
{
    private UtilisateursService $utilisateurService;
    private VueUtilisateursService $vueUtilisateursService;


    public function __construct(UtilisateursService $utilisateurService, VueUtilisateursService $vueUtilisateursService)
    {
        $this->utilisateurService = $utilisateurService;
        $this->vueUtilisateursService = $vueUtilisateursService;
    }
    #[Route('', name: 'user', methods: ['GET'])]
    // #[TokenRequired(['Admin'])]
    public function getUtilisateur(Request $request): JsonResponse
    {
        try {
            $dateParam = $request->query->get('date');
            $limitParam = $request->query->get('limit');
            $date = $dateParam ? new DateTimeImmutable($dateParam) : new DateTimeImmutable();
            $limit = $limitParam ? (int)$limitParam : 10; 
            $paginationCriteria = new PaginationCriteria($date, $limit);    
            $users = $this->utilisateurService->getAllUsersPaginated($paginationCriteria);

            $excludes = ['deletedAt','mdp'];
            $data = $this->utilisateurService->transformerArray($users, $excludes);
            return $this->jsonSuccess($data);

        } catch (\Exception $e) {
            return $this->jsonError($e->getMessage(), $e->getCode() ?: 400);
        }

    }

    #[Route('', name: 'api_utilisateur_create', methods: ['POST'])]
    #[TokenRequired(['Admin'])]
    public function createUser(Request $request): JsonResponse
    {
        try {
            $dto = $this->deserializeAndValidate(
                $request,
                UtilisateursDto::class
            );
            $user = $this->utilisateurService->saveDto($dto);
            
           $excludes = ['createdAt', 'deletedAt','mdp'];
            $data = $user->toArray($excludes);
            return $this->jsonSuccess($data);

        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }

    #[Route('/{id}', methods: ['GET'], requirements: ['id' => '\d+'])]
    #[TokenRequired(['Admin'])]
    public function getOneUser(int $id): JsonResponse
    {
        try {
            $user =$this->utilisateurService->getById($id);

            if (!$user) {
                return $this->jsonError('Utilisateur non trouvé', 404);
            }

            $excludes = ['createdAt', 'deletedAt'];
            $data = $user->toArray($excludes);
            return $this->jsonSuccess($data);
        } catch (\Exception $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }

    #[Route('/{id}', name: 'api_utilisateur_update', methods: ['PUT'])]
    #[TokenRequired(['Admin'])]
    public function updateUser(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->jsonError('Données invalides ou JSON mal formé', 400);
        }

        try {
            $user = $this->utilisateurService->updateUser($id, $data);
            
            $excludes = ['createdAt', 'deletedAt'];
            $data = $user->toArray($excludes);
            return $this->jsonSuccess($data);
            
        } catch (\Exception $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }



    #[Route('/login', name: 'api_utilisateur_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $requiredFields = ['email', 'mdp'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Champs requis manquants',
                'missingFields' => $missingFields
            ], 400);
        }

        $email = $data['email'];
        $plainPassword = $data['mdp'];


        // 🔑 Vérification du login via le repository
        $user = $this->utilisateurService->login($email, $plainPassword);

        if (!$user) {
            return $this->jsonError('Identifiants invalides', 404);
        }

        $excludes = ['createdAt', 'deletedAt','mdp'];
        $userArray = $user->toArray($excludes);

        $tokenDuration = $this->params->get('jwt_token_duration');

        $token = $this->jwtManager->createToken($userArray, $tokenDuration);
        $tokenString = $token->toString();
        $data = [
            'membre' => $userArray,
            'token' => $tokenString
        ];
        return $this->jsonSuccess($data);
    }
    #[Route('/changerMdp', name: 'user_changer_mdp', methods: ['POST'])]
    #[TokenRequired]
    public function changerMdp(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $user = $this->getUserFromRequest($request);
            $requiredFields = ['mdp'];
            $this->validatorService->validateRequiredFields($data, $requiredFields);

            $nouveauMdp = $data['mdp'];
            $user = $this->utilisateursService->changerMdp($user, $nouveauMdp);
            
            return $this->jsonSuccess($user->toArray(['createdAt', 'deletedAt','mdp']));
            
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }
    #[Route('/recherche', name: 'api_utilisateurs_recherche', methods: ['POST'])]
    #[TokenRequired(['Utilisateurs'])]
    public function recherche(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $body = $request->toArray();
            $jerena = $body['nomComplet'] ?? "";
            $dateParam = $body['date'] ?? null;
            $date = $dateParam ? new DateTimeImmutable($dateParam) : new DateTimeImmutable();
            $limit = $_ENV['LIMIT_PAGINATIONS'] ?? 10; 
            $paginationCriteria = new PaginationCriteria($date, $limit);
            $orderCriteria = new OrderCriteria();
            $utlisateurs = $this->vueUtilisateursService->rechercheByNomPrenom($user, $jerena,$orderCriteria, $paginationCriteria);
            
            $data = $this->vueUtilisateursService->transformerArrayJson($utlisateurs);
            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }
}


