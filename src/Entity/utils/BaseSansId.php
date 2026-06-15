<?php

namespace App\Entity\utils;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class BaseSansId
{
    #[ORM\Column(type: "datetime_immutable")]
    protected ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    protected ?\DateTimeImmutable $deletedAt = null;


    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }


    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): static
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


    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }


    public function toArray(array $exclude = []): array
    {
        $reflection = new \ReflectionClass(static::class);
        $data = [];

        foreach ($reflection->getProperties() as $property) {

            $propertyName = $property->getName();

            if (in_array($propertyName, $exclude, true)) {
                continue;
            }

            $property->setAccessible(true);

            if (!$property->isInitialized($this)) {
                continue;
            }

            $value = $property->getValue($this);

            if ($value instanceof \DateTimeInterface) {
                $data[$propertyName] = $value->format('Y-m-d H:i:s');
                continue;
            }

            if (is_object($value) || is_resource($value)) {
                continue;
            }

            $data[$propertyName] = $value;
        }

        return $data;
    }
}