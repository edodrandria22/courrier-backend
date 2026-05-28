<?php

namespace App\Dto\utils;

class OrderCriteria
{
    private string $field;
    private string $direction;

    private const ALLOWED_FIELDS = [
        'id',
        'dateDebut',
        'dateFin',
        'createdAt',
    ];

    public function __construct(
        string $field = 'createdAt',
        string $direction = 'DESC'
    ) {
        $this->setField($field);
        $this->setDirection($direction);
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function setField(string $field): void
    {
        $this->field = in_array($field, self::ALLOWED_FIELDS, true)
            ? $field
            : 'createdAt';
    }

    public function setDirection(string $direction): void
    {
        $direction = strtoupper($direction);
        $this->direction = $direction === 'ASC' ? 'ASC' : 'DESC';
    }
}