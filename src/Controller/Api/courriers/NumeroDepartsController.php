<?php

namespace App\Controller\Api\courriers;

use App\Controller\Api\utils\BaseApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Annotation\TokenRequired;
use App\Dto\courriers\NumeroDepartDto;
use App\Service\courriers\NumeroCourriersService;

#[Route('/numeroDeparts')]
class NumeroDepartsController extends BaseApiController
{

    public function __construct(
        private readonly NumeroCourriersService $numeroCourriersService,
    ) {
        
    }
    #[Route('', name: 'api_numero_departs_index', methods: ['GET'])]
    #[TokenRequired(['Utilisateur'])]
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $isSend = $request->query->get('isSend');
            $data = ['isSend' => $isSend];
            $this->validatorService->validateRequiredFields($data,['isSend']);
            $isSendBool = match ($isSend) {
                'true', '1', 'yes', 'on' => true,
                'false', '0', 'no', 'off' => false,
                default => throw new \Exception('Valeur isSend invalide'),
            };
            $numeroDepart = $this->numeroCourriersService->getNumeroDepartActuel($user, $isSendBool, date('Y'));
            
            $excludes = ["createdAt","deletedAt","id"];
            $data = $numeroDepart->toArray($excludes);
            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }
    #[Route('', name: 'api_numero_departs_creer', methods: ['POST'])]
    #[TokenRequired(['Utilisateur'])]
    public function creer(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $dto = $this->deserializeAndValidate(
                $request,
                NumeroDepartDto::class
            );
            $numeroDepart = $this->numeroCourriersService->saveDto($user, $dto);
            $excludes = ['createdAt','deletedAt'];
            $data = $numeroDepart->toArray($excludes);
            return $this->jsonSuccess($data);

        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }

}
