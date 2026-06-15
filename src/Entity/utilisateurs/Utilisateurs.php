<?php

namespace App\Entity\utilisateurs;

use App\Repository\utilisateurs\UtilisateursRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateursRepository::class)]
class Utilisateurs extends BaseUtilisateurs
{
    
}
