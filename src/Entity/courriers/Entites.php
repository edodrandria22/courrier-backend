<?php

namespace App\Entity\courriers;

use App\Entity\utils\BaseNom;
use App\Repository\courriers\EntitesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntitesRepository::class)]
class Entites extends BaseNom
{
}