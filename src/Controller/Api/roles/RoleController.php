<?php

namespace App\Controller\Api\roles;

use App\Controller\Api\utils\BaseApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Annotation\TokenRequired;
use App\Service\utilisateurs\RolesService;

#[Route('/roles')]
class RoleController extends BaseApiController
{
    private RolesService $rolesService;
    

    public function __construct(RolesService $rolesService)
    {
        $this->rolesService = $rolesService;
    }
    #[Route('', name: 'api_roles_index', methods: ['GET'])]
    #[TokenRequired(['Admin'])]
    public function index(): JsonResponse
    {
        try {
            $rolesId= [1,2];
            $roles = $this->rolesService->getByRole($rolesId);
            $excludes = ["createdAt","deletedAt"];
            $data = $this->rolesService->transformerArray($roles, $excludes);

            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }
}
