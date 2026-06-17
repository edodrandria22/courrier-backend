<?php

namespace App\Entity\messages;

use App\Entity\utils\BaseValidation;
use App\Repository\messages\MessagesRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use App\Entity\courriers\Courriers;
use App\Entity\utilisateurs\Utilisateurs;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\utils\Fichiers;

#[ORM\Entity(repositoryClass: MessagesRepository::class)]
class Messages extends BaseValidation
{
    #[ORM\ManyToOne(targetEntity: Courriers::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Courriers $courrier = null;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $expediteur = null;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $destinataire = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $isReadAt = null;


    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observation = null;

    #[ORM\OneToMany(mappedBy: 'message', targetEntity: Fichiers::class, cascade: ['persist', 'remove'])]
    private Collection $fichiers;
    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $numeroExpediteur = null;
    
    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $numeroDestinataire = null;

    public function __construct()
    {
        $this->fichiers = new ArrayCollection();
    }


    public function getCourrier(): ?Courriers
    {
        return $this->courrier;
    }

    public function setCourrier(?Courriers $courrier): static
    {
        $this->courrier = $courrier;
        return $this;
    }

    public function getExpediteur(): ?Utilisateurs
    {
        return $this->expediteur;
    }

    public function setExpediteur(?Utilisateurs $expediteur): static
    {
        $this->expediteur = $expediteur;
        return $this;
    }

    public function getDestinataire(): ?Utilisateurs
    {
        return $this->destinataire;
    }

    public function setDestinataire(?Utilisateurs $destinataire): static
    {
        $this->destinataire = $destinataire;
        return $this;
    }

    public function getIsReadAt(): ?\DateTimeInterface
    {
        return $this->isReadAt;
    }

    public function setIsReadAt(?\DateTimeInterface $isReadAt): static
    {
        $this->isReadAt = $isReadAt;
        return $this;
    }
    public function getNumeroExpediteur(): ?int
    {
        return $this->numeroExpediteur;
    }
    public function setNumeroExpediteur(?int $numeroExpediteur): static
    {
        $this->numeroExpediteur = $numeroExpediteur;
        return $this;
    }
    public function getNumeroDestinataire(): ?int
    {
        return $this->numeroDestinataire;
    }
    public function setNumeroDestinataire(?int $numeroDestinataire): static
    {
        $this->numeroDestinataire = $numeroDestinataire;
        return $this;
    }


    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): static
    {
        $this->observation = $observation;
        return $this;
    }

    /**
     * @return Collection<int, Fichiers>
     */
    public function getFichiers(): Collection
    {
        return $this->fichiers;
    }

    public function addFichier(Fichiers $fichier): self
    {
        if (!$this->fichiers->contains($fichier)) {
            $this->fichiers->add($fichier);
            $fichier->setMessage($this);
        }

        return $this;
    }

    public function removeFichier(Fichiers $fichier): self
    {
        if ($this->fichiers->removeElement($fichier)) {
            // set the owning side to null (unless already changed)
            if ($fichier->getMessage() === $this) {
                $fichier->setMessage(null);
            }
        }

        return $this;
    }

    public function toArray(array $exclude = []): array
    {
        $data = parent::toArray($exclude);
        // $data['courrier'] = $this->courrier?->toArray($exclude);

        $excludeUsers = array_merge($exclude, ['mdp','role']);
        $data['expediteur'] = $this->expediteur?->toArray($excludeUsers);
        $data['destinataire'] = $this->destinataire?->toArray($excludeUsers);
        $data['fichiers'] = $this->fichiers->map(fn(Fichiers $f) => $f->toArray($exclude))->toArray();
        return $data;
    }
    public function getParticipantsHtml(): string
    {
        $expediteur = $this->getExpediteur();
        $destinataire = $this->getDestinataire();

        $expNom = $expediteur 
            ? trim(($expediteur->getNom() ?? '') . ' ' . ($expediteur->getPrenom() ?? ''))
            : 'Inconnu';

        $destNom = $destinataire 
            ? trim(($destinataire->getNom() ?? '') . ' ' . ($destinataire->getPrenom() ?? ''))
            : 'Inconnu';

        // 👉 date du message (createdAt venant de BaseValidation)
        $date = $this->getCreatedAt()?->format('d/m/Y H:i') ?? 'Date inconnue';

        return "
            <div style='padding:10px;border:1px solid #ddd;border-radius:8px;margin-bottom:10px'>
                <div style='font-size:12px;color:#888;margin-bottom:5px'>
                    {$date}
                </div>
                <strong>Expéditeur :</strong> {$expNom} <br>
                <strong>Destinataire :</strong> {$destNom}
            </div>
        ";
    }
}
