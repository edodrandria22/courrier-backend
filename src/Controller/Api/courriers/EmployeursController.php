<?php

namespace App\Controller\Api\courriers;

use App\Controller\Api\utils\BaseApiController;
use App\Dto\utils\OrderCriteria;
use App\Service\courriers\EmployeursService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/employeurs')]
class EmployeursController extends BaseApiController
{

    public function __construct(
        private readonly EmployeursService $employeursService,
    ) {
        
    }
    #[Route('', name: 'api_employeurs', methods: ['GET'])]
    public function index(): JsonResponse
    {
        try {

            $employeurs = $this->employeursService->getAll(new OrderCriteria("createdAt", "ASC"));
            $excludes = ["createdAt","deletedAt"];
            $data = $this->employeursService->transformerArray($employeurs, $excludes);
            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }
    

}
