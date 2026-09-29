<?php

namespace App\Service\utils;

use App\Dto\FichierValidationDto;
use App\Dto\utils\ConditionCriteria;
use App\Entity\courriers\CourrierValidations;
use App\Entity\utils\FichiersValidations;
use App\Repository\utils\FichiersValidationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FichiersValidationsService extends BaseService
{
    /**
     * Convertit un fichier uploadé en entité Fichiers avec stockage BLOB
     */
    public function __construct(
        EntityManagerInterface $em,
        private readonly FichiersValidationsRepository $repo,
        // private readonly FichiersValidationsService $fichiersValidationsService,
    ) {
        parent::__construct($em);
    }
    
    protected function getRepository()
    {
        return $this->repo;
    }
    
    public function saveToBlob(FichierValidationDto $dto): FichiersValidations
    {
        $fichier = new FichiersValidations();
        $file = $dto->getFichier();
        // Lecture du contenu binaire
        $binaryContent = file_get_contents($file->getPathname());

        $fichier->setNom($file->getClientOriginalName())
            ->setType($file->getMimeType())
            ->setTypeFichier($dto->getTypeFichier())
            ->setBinaire($binaryContent);
    

        return $fichier;
    }
    public function persistFiles(array $files, CourrierValidations $courrierValidation): array
    {
        $result =[];
        foreach ($files as $file) {
            if ($file instanceof FichierValidationDto) {
                $fichierEntity = $this->saveToBlob($file);
                $fichierEntity->setCourrierValidation($courrierValidation);   
                $this->em->persist($fichierEntity);
                $courrierValidation->addFichier($fichierEntity);
                $result[] = $fichierEntity;
            }
        }
        $this->em->flush();
        return $result;
    }
 
    public function loadFromBlob(FichiersValidations $fichier): UploadedFile
    {
        // Création d'un fichier temporaire sur le disque
        $tmpPath = tempnam(sys_get_temp_dir(), 'fichier_validation_');

        // Écriture du contenu binaire dans ce fichier temporaire
        file_put_contents($tmpPath, $fichier->getBinaire());

        // Reconstruction de l'UploadedFile
        return new UploadedFile(
            $tmpPath,
            $fichier->getNom(),
            $fichier->getType(),
            null,   // taille de l'erreur (UPLOAD_ERR_OK par défaut)
            true    // mode "test" : bypass de is_uploaded_file()
        );
    }
    public function getByCourrierValidationId(int $courrierValidationId): array
    {
        
         $conditions = [
            new ConditionCriteria('courrierValidation', $courrierValidationId, '='),
        ];
        return $this->search($conditions);
    }
    public function getByCourrierValidationIdUploaded(int $courrierValidationId): array
    {
        $courrierValidations = $this->getByCourrierValidationId($courrierValidationId);
        $result = [];
        foreach ($courrierValidations as $courrierValidation) {
            $result[] = $this->loadFromBlob($courrierValidation);
        }
        return $result;
    }
}
