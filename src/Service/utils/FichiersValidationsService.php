<?php

namespace App\Service\utils;

use App\Dto\FichierValidationDto;
use App\Entity\courriers\CourrierValidations;
use App\Entity\utils\FichiersValidations;
use App\Repository\utils\FichiersValidationsRepository;
use Doctrine\ORM\EntityManagerInterface;

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
}
