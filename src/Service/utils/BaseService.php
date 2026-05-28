<?php

namespace App\Service\utils;

use App\Dto\utils\OrderCriteria;
use App\Dto\utils\PaginationCriteria;
// use App\Entity\utils\BaseEntite;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
abstract class BaseService
{
    /**
     * Chaque service enfant doit retourner son repository
     */
    protected EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
    }

    abstract protected function getRepository();

    /**
     * Récupère une entité par son ID
     */
    public function getById(int $id): ?object
    {
        return $this->getRepository()->getById($id);
    }
    public function throwIfNull(mixed $data, string $message): void
    {
        if ($data === null) {
            throw new NotFoundHttpException($message);
        }
    }


    public function getVerifierById(int $id): object
    {
        $entity = $this->getById($id);
        $shortName = (new \ReflectionClass($this->getRepository()->getClassName()))->getShortName();
        $this->throwIfNull($entity, "$shortName introuvable pour l'ID $id.");
        return $entity;
    }

    public function getAll(?OrderCriteria $orderCriteria = new OrderCriteria()): array
    {
        return $this->getRepository()->getAll($orderCriteria);
    }

    // /**
    //  * Transforme un tableau d'entités en tableau simple
    //  * @param BaseEntite[] $entities
    //  */
    public function transformerArray(array $entities, array $exclude = []): array
    {
        $items = [];

        foreach ($entities as $entity) {
            $items[] = $entity->toArray($exclude);
        }

        return $items;
    }
    public function save(object $entity, bool $flush = true): object
    {
        $this->em->persist($entity);

        if ($flush) {
            $this->em->flush();
        }

        return $entity;
    }
    public function delete(?object $entity, ?\DateTimeImmutable $deleteAt = null): void
    {
        if ($entity === null) {
            return;
        }

        $entity->setDeletedAt($deleteAt ?? new \DateTimeImmutable());
        $this->em->flush();
    }
    public function search(
        array $conditions = [],
        ?OrderCriteria $orderCriteria = null,
        ?PaginationCriteria $pagination = null,
        array $joins = []
    ): array {
        return $this->getRepository()->search($conditions, $orderCriteria, $pagination, $joins);
    }
        // public function findByCourrier(Courriers $courrier, ?OrderCriteria $orderCriteria = null): array
    // {
    //     // Définir la condition sur le courrier
        // $conditions = [
        //     new ConditionCriteria('courrier', $courrier, '=')
        // ];

    //     // Définir les relations à joindre
    //     $joins = [
    //         new JoinCriteria('m.expediteur', 'e', 'LEFT'),
    //         new JoinCriteria('m.destinataire', 'd', 'LEFT')
    //     ];

    //     // Appeler la méthode search factorisée
    //     return $this->search(
    //         conditions: $conditions,
    //         order: $orderCriteria,
    //         joins: $joins
    //     );
    // }

}