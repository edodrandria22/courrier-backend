<?php

namespace App\Controller\Api\courriers;

use App\Controller\Api\utils\BaseApiController;
use App\Service\courriers\EntitesService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/entites')]
class NumeroDepartsController extends BaseApiController
{

    public function __construct(
        private readonly EntitesService $entitesService,
    ) {
        
    }
    #[Route('', name: 'api_entites', methods: ['GET'])]
    public function index(): JsonResponse
    {
        try {

            $entites = $this->entitesService->getAll();
            $excludes = ["createdAt","deletedAt"];
            $data = $this->entitesService->transformerArray($entites, $excludes);
            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }
    

}
