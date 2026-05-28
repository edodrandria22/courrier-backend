<?php

namespace App\Controller\Api\roles;

use App\Controller\Api\utils\BaseApiController;
use App\Repository\utilisateurs\RolesRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Annotation\TokenRequired;

#[Route('/roles')]
class RoleController extends BaseApiController
{
    #[Route('', name: 'api_roles_index', methods: ['GET'])]
    #[TokenRequired(['Admin'])]
    public function index(RolesRepository $roleRepository): JsonResponse
    {
        try {
            $roles = $roleRepository->findAll();

            $data = array_map(function ($role) {
                return [
                    'id' => $role->getId(),
                    'nom' => $role->getName(),
                ];
            }, $roles);

            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }
}
