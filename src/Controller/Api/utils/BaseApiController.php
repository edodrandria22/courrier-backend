<?php

namespace App\Controller\Api\utils;

use App\Entity\utilisateurs\Utilisateurs;
use App\Service\utilisateurs\UtilisateursService;
use App\Service\utils\JwtTokenManager;
use App\Service\utils\ValidationService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Service\Attribute\Required;
abstract class BaseApiController extends AbstractController
{
    public JwtTokenManager $jwtManager;
    public UtilisateursService $utilisateursService;
    public ValidationService $validatorService;
    public SerializerInterface $serializer;
    
    public ValidatorInterface $validator;
    public ParameterBagInterface $params;
    #[Required]
    public function setDependencies(
        JwtTokenManager $jwtManager,
        UtilisateursService $utilisateursService,
        ValidationService $validatorService,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        ParameterBagInterface $params
    ) {
        $this->jwtManager = $jwtManager;
        $this->utilisateursService = $utilisateursService;
        $this->validatorService = $validatorService;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->params = $params;
    }

    /**
     * Récupère l'utilisateur à partir du token JWT présent dans la requête
     */
    protected function getUserFromRequest(Request $request): Utilisateurs
    {
        $token = $this->jwtManager->extractTokenFromRequest($request);
        if (!$token) {
            throw new AccessDeniedHttpException("Token manquant.");
        }

        $claims = $this->jwtManager->extractClaimsFromToken($token);
        if (!$claims || !isset($claims['id'])) {
            throw new AccessDeniedHttpException("Token invalide ou corrompu.");
        }

        $userId = (int) $claims['id'];
        $user = $this->utilisateursService->getById($userId);

        $this->validatorService->throwIfNull($user, "Utilisateur introuvable pour l'ID $userId.");

        return $user;
    }

    /**
     * Retourne une réponse JSON de succès
     */
    protected function jsonSuccess(mixed $data, int $code = 200): JsonResponse
    {
        return $this->json([
            'status' => 'success',
            'data' => $data
        ], $code);
    }
    

    /**
     * Retourne une réponse JSON d'erreur
     */
    protected function jsonError(string $message, int $code = 400): JsonResponse
    {
        return $this->json([
            'status' => 'error',
            'message' => $message
        ], $code);
    }
    
    protected function deserializeAndValidate(Request $request, string $dtoClass)
    {
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            $dtoClass,
            'json'
        );

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            $messages = [];

            foreach ($errors as $error) {
                $property = $error->getPropertyPath();
                $message = $error->getMessage();
                $messages[] = sprintf('%s : %s', $property, $message);
            }

            $erreurMessage = 'Erreur de validation : ' . implode(' | ', $messages);

            throw new Exception($erreurMessage);
        }

        return $dto;
    }

}
