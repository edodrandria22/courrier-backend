<?php

namespace App\Controller\Api\courriers;

use App\Controller\Api\utils\BaseApiController;
use App\Dto\FichierValidationDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Annotation\TokenRequired;
use App\Dto\courriers\CourriersValidationsDto;
use App\Service\courriers\CourrierValidationsService;
use DateTimeImmutable;

#[Route('/courriersValidations')]
class CourrierValidationController extends BaseApiController
{
    public function __construct(
        private readonly CourrierValidationsService $courrierValidationService
    ) {
        
    }

    #[Route('', name: 'api_courriers_validation_list', methods: ['GET'])]
    #[TokenRequired(['Om'])]
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $dateParam = $request->query->get('date');
            $date = $dateParam ? new DateTimeImmutable($dateParam) : new DateTimeImmutable();
            $limitParam = $request->query->get('limit');
            $isValid = $request->query->get('isValid');
            $limit = $limitParam ? (int)$limitParam : ($_ENV['LIMIT_PAGINATIONS'] ?? 10);
            $courriersValidations = $this->courrierValidationService->getByUser($user,$date,$limit,$isValid);
            // $courriersValidations = $this->courrierValidationService->getAll();
            $data = $this->courrierValidationService->transformerArrayEnJson($courriersValidations);
            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }
    #[Route('', name: 'api_courriers_validation_creer', methods: ['POST'])]
    #[TokenRequired(['Om'])]
    public function creer(Request $request): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $dto = $this->deserializeFormDataAndValidate(
                $request,
                CourriersValidationsDto::class
            );
            $uploadedFilesDemande = $request->files->get('demande', null);
            $uploadedFilesInvition = $request->files->get('lettreInvitation', null);
            $uploadedFilePlanVol = $request->files->get('planVol', null);

            $fichiers[] = new FichierValidationDto('demande',$uploadedFilesDemande);
            $fichiers[] = new FichierValidationDto('lettreInvitation',$uploadedFilesInvition);
            $fichiers[] = new FichierValidationDto('planVol',$uploadedFilePlanVol);
            $this->validateDtos($fichiers);
            $courrierValidation = $this->courrierValidationService->saveDto($user,$dto,$fichiers);
            $data = $this->courrierValidationService->tranformerEnJson($courrierValidation);
            return $this->jsonSuccess($data);

        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }
    #[Route('/{id}', name: 'api_courriers_validation_update', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[TokenRequired(['Om'])]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $user = $this->getUserFromRequest($request);
            $dto = $this->deserializeFormDataAndValidate(
                $request,
                CourriersValidationsDto::class
            );
            $uploadedFilesDemande = $request->files->get('demande', null);
            $uploadedFilesInvition = $request->files->get('lettreInvitation', null);
            $uploadedFilePlanVol = $request->files->get('planVol', null);

            $fichiers[] = new FichierValidationDto('demande',$uploadedFilesDemande);
            $fichiers[] = new FichierValidationDto('lettreInvitation',$uploadedFilesInvition);
            $fichiers[] = new FichierValidationDto('planVol',$uploadedFilePlanVol);
            $this->validateDtos($fichiers);
            $courrier = $this->courrierValidationService->updateDtoId($user, $id, $dto, $fichiers);
            $data = $this->courrierValidationService->tranformerEnJson($courrier);
            return $this->jsonSuccess($data);

        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }
    #[Route('/{id}/remarque', name: 'api_courriers_validation_add_remarque', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[TokenRequired(['Superviseur','Admin'])]
    public function addRemarque(Request $request, int $id): JsonResponse
    {
        try {
            $data = $request->toArray();
            $remarque = $data['remarque'] ?? null;
            if (!$remarque) {
                return $this->jsonError('La remarque est obligatoire', 400);
            }
            $courrier = $this->courrierValidationService->addRemarque($id, $remarque);
            $data = $this->courrierValidationService->tranformerEnJson($courrier);
            return $this->jsonSuccess($data);

        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }
    #[Route('/{id}/valider', name: 'api_courriers_validation_valider', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[TokenRequired(['Superviseur','Admin'])]
    public function valider(Request $request, int $id): JsonResponse
    {
        try {
            $courrier = $this->courrierValidationService->validerCourrierValidation($id);
            $data = $this->courrierValidationService->tranformerEnJson($courrier);
            return $this->jsonSuccess($data);

        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(),  400);
        }
    }
}
        

