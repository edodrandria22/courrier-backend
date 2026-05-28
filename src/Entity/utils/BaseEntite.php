<?php

namespace App\Entity\utils;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class BaseEntite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(type: "datetime_immutable")]
    protected ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    protected ?\DateTimeImmutable $deletedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function delete(): void
    {
        $this->deletedAt = new \DateTimeImmutable();
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function restore(): void
    {
        $this->deletedAt = null;
    }

    // 🔥 Automatique à l’insertion
    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * Convertit l'entité en tableau simple (pour JSON)
     * Utilise la réflexion pour extraire les propriétés scalaires et les dates.
     */
    public function toArray(array $exclude = []): array
    {
        // $reflection = new \ReflectionClass($this);
        $reflection = new \ReflectionClass(static::class);
        $data = [];

        foreach ($reflection->getProperties() as $property) {
            $propertyName = $property->getName();

            // Si la propriété est dans la liste des exclusions, on l'ignore
            if (in_array($propertyName, $exclude, true)) {
                continue;
            }

            $property->setAccessible(true);

            // On vérifie si la propriété est initialisée
            if (!$property->isInitialized($this)) {
                continue;
            }

            $value = $property->getValue($this);

            // Formatage des dates
            if ($value instanceof \DateTimeInterface) {
                $data[$propertyName] = $value->format('Y-m-d H:i:s');
                continue;
            }

            // Ignorer les objets complexes (relations) et les resources PHP (ex: BLOB stream)
            if (is_object($value) || is_resource($value)) {
                continue;
            }

            $data[$propertyName] = $value;
        }

        return $data;
    }
}
