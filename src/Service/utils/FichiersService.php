<?php

namespace App\Service\utils;

use App\Entity\messages\Messages;
use App\Entity\utils\Fichiers;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FichiersService
{
    /**
     * Convertit un fichier uploadé en entité Fichiers avec stockage BLOB
     */
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
    }
    public function saveToBlob(UploadedFile $file): Fichiers
    {
        $fichier = new Fichiers();

        // Lecture du contenu binaire
        $binaryContent = file_get_contents($file->getPathname());

        $fichier->setNom($file->getClientOriginalName())
            ->setType($file->getMimeType())
            ->setBinaire($binaryContent);

        return $fichier;
    }
    public function persistFiles(array $files, Messages $message): array
    {
        $result =[];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $fichierEntity = $this->saveToBlob($file);
                $fichierEntity->setMessage($message);   
                $this->em->persist($fichierEntity);
                $message->addFichier($fichierEntity);
                $result[] = $fichierEntity;
            }
        }
        $this->em->flush();
        return $result;
    }
}
