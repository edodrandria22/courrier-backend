<?php

namespace App\Entity\courriers;

use App\Entity\utils\BaseNom;
use App\Repository\courriers\EmployeursRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeursRepository::class)]
class Employeurs extends BaseNom
{
}