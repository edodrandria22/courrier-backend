<?php

namespace App\Dto\utils;

class PaginationCriteria
{
    private mixed $value = null;
    private int $limit = 10;

    public function __construct(
        mixed $value = null,
        int $limit = 10
    ) {
        $this->value = $value;
        $this->limit = $limit;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

}