<?php

namespace App\Dto\courriers;

use Symfony\Component\Validator\Constraints as Assert;

class RechercheCourriersDto
{
    #[Assert\Type("string")]
    public ?string $reference = null;

    #[Assert\Type("string")]
    public ?string $object = null;

    #[Assert\Type("string")]
    public ?string $nom = null;

    #[Assert\Type("string")]
    public ?string $prenom = null;

    #[Assert\Type("string")]
    public ?string $email = null;

    #[Assert\Type("string")]
    public ?string $telephone = null;

    #[Assert\Type(\DateTimeInterface::class)]
    public ?\DateTimeInterface $dateDebut = null;

    #[Assert\Type(\DateTimeInterface::class)]
    public ?\DateTimeInterface $dateFin = null;

    #[Assert\Type("integer")]
    public ?int $numero = null;

    #[Assert\Choice(choices: ["en_cours", "finalise"])]
    public ?string $statut = null;
    #[Assert\Type(\DateTimeInterface::class)]
    public ?\DateTimeInterface $date = null;
}