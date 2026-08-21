<?php

namespace App\Dto\utils;

class OrderCriteria
{
    private array $field;
    private string $direction;

    private const ALLOWED_FIELDS = [
        'id',
        'dateDebut',
        'dateFin',
        'createdAt',
        'dateMessage',
        'historiqueId',
        'isTraiterAt',
        'isReadAt'
    ];

    public function __construct(
        string|array $field = 'createdAt',
        string $direction = 'DESC'
    ) {
        $this->setField($field);
        $this->setDirection($direction);
    }

    public function getField(): array
    {
        return $this->field;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function setField(string|array $field): void
    {
        $fields = is_array($field) ? $field : [$field];

        $this->field = array_values(array_filter(
            $fields,
            fn(string $value) => in_array($value, self::ALLOWED_FIELDS, true)
        ));

        if (empty($this->field)) {
            $this->field = ['createdAt'];
        }
    }

    public function setDirection(string $direction): void
    {
        $direction = strtoupper($direction);
        $this->direction = $direction === 'ASC' ? 'ASC' : 'DESC';
    }
    public function addField(string|array $field): void
    {
        $fields = is_array($field) ? $field : [$field];

        $this->field = array_values(array_filter(
            $fields,
            fn(string $value) => in_array($value, self::ALLOWED_FIELDS, true)
        ));

        if (empty($this->field)) {
            $this->field = ['createdAt'];
        }
    }
}