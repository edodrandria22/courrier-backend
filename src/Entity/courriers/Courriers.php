<?php

namespace App\Entity\courriers;

use App\Entity\courriers\BaseCourriers;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\courriers\CourriersRepository;

#[ORM\Entity(repositoryClass: CourriersRepository::class)]
class Courriers extends BaseCourriers
{
}
