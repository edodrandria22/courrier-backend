<?php

namespace App\Dto\utils;

class JoinCriteria
{
    private string $relation;
    private string $alias;
    private string $type;

    public function __construct(string $relation, string $alias, string $type = 'LEFT')
    {
        $this->relation = $relation;
        $this->alias = $alias;
        $this->type = strtoupper($type);
    }

    public function getRelation(): string
    {
        return $this->relation;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getType(): string
    {
        return $this->type;
    }
}