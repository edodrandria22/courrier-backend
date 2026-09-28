<?php

namespace App\Controller\Api\courriers;

use App\Controller\Api\utils\BaseApiController;
use App\Dto\FichierValidationDto;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Annotation\TokenRequired;
use App\Dto\courriers\CourriersValidationsDto;
use App\Service\courriers\CourrierValidationsService;
#[Route('/courriersValidations')]
class CourrierValidationController extends BaseApiController
{
    public function __construct(
        private readonly CourrierValidationsService $courrierValidationService
    ) {
        
    }

    #[Route('', name: 'api_courriers_validation_list', methods: ['GET'])]
    #[TokenRequired(['Utilisateur'])]
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $dateParam = $request->query->get('date');
            $date = $dateParam ? new DateTimeImmutable($dateParam) : new DateTimeImmutable();
            $limitParam = $request->query->get('limit');
            $limit = $limitParam ? (int)$limitParam : ($_ENV['LIMIT_PAGINATIONS'] ?? 10);
            $courrier = $this->courrierValidationService->getAll();
            $data = $this->courrierValidationService->transformerArray($courrier,["deletedAt"]);
            return $this->jsonSuccess($courrier);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }
    #[Route('', name: 'api_courriers_validation_creer', methods: ['POST'])]
    #[TokenRequired(['OM'])]
    public function creer(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $dto = $this->deserializeFormDataAndValidate(
                $request,
                CourriersValidationsDto::class
            );
            // $dto = $this->deserializeAndValidate(
            //     $request,
            //     CourriersValidationsDto::class
            // );
            $uploadedFilesCin = $request->files->get('cin', null);
            $uploadedFilesPassport = $request->files->get('passeport', null);
            $fichiers[] = new FichierValidationDto('cin',$uploadedFilesCin);
            $fichiers[] = new FichierValidationDto('passeport',$uploadedFilesPassport);
            $this->validateDtos($fichiers);
            $courrierValidation = $this->courrierValidationService->saveDto($user,$dto,$fichiers);
            $excludes = ['deletedAt'];
            $data = $courrierValidation->toArray($excludes);
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
                CourriersValidationsDto::class
            );
            $courrier = $this->courrierValidationService->updateDtoId($user, $id, $dto);
            $excludes = ['deletedAt','dateValidation','cloturerPar'];
            $data = $courrier->toArray($excludes);
            return $this->jsonSuccess($data);

        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }

        

}
