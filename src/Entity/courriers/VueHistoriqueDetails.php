<?php

namespace App\Entity\courriers;

use App\Entity\courriers\BaseCourriers;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\courriers\VueHistoriqueDetailsRepository;

#[ORM\Entity(repositoryClass: VueHistoriqueDetailsRepository::class, readOnly: true)]
#[ORM\Table(name: "vue_historique_details")]
#[ORM\MappedSuperclass]
class VueHistoriqueDetails extends BaseCourriers
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private ?int $historiqueId = null;

    #[ORM\Column(type: "integer")]
    private ?int $id = null;
    
    #[ORM\Column(type: "integer")]
    private ?int $utilisateurId = null;
    
    
    #[ORM\Column(type: "integer")]
    private ?int $destinataireId = null;

    #[ORM\Column(type: "integer")]
    private ?int $expediteurId = null;
    
    
    #[ORM\Column(type: "integer")]
    private ?int $messageId = null;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?\DateTimeImmutable $isReadAt = null;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $isSend = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $numero = null;
    
    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $numRef = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observation = null;
    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    protected ?\DateTimeImmutable $dateReception = null;
    /**
 * @var DetailPersonnes[]
 */
    private array $detailPersonnes = [];

    public function getHistoriqueId(): ?int
    {
        return $this->historiqueId;
    }
    
    public function setHistoriqueId(?int $historiqueId): self
    {
        $this->historiqueId = $historiqueId;
        return $this;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }
    
    public function getUtilisateurId(): ?int
    {
        return $this->utilisateurId;
    }
    
    public function setUtilisateurId(?int $utilisateurId): self
    {
        $this->utilisateurId = $utilisateurId;
        return $this;
    }
    
    public function getMessageId(): ?int
    {
        return $this->messageId;
    }
    
    public function setMessageId(?int $messageId): self
    {
        $this->messageId = $messageId;
        return $this;
    }
    
    public function getIsReadAt(): ?\DateTimeImmutable
    {
        return $this->isReadAt;
    }
    
    public function setIsReadAt(?\DateTimeImmutable $isReadAt): self
    {
        $this->isReadAt = $isReadAt;
        return $this;
    }
    public function setExpediteurId(?int $expediteurId): self
    {
        $this->expediteurId = $expediteurId;
        return $this;
    }
    public function getExpediteurId(): ?int 
    {
        return $this->expediteurId;
    }
    public function setDestinataireId(?int $destinataireId): self
    {
        $this->destinataireId = $destinataireId;
        return $this;
    }
    public function getDestinataireId(): ?int
    {
        return $this->destinataireId;
    }
    
    public function getIsSend(): ?bool
    {
        return $this->isSend;
    }
    
    public function setIsSend(?bool $isSend): self
    {
        $this->isSend = $isSend;
        return $this;
    }
    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(?int $numero): self
    {
        $this->numero = $numero;
        return $this;
    }
    public function getNumRef(): ?int
    {
        return $this->numRef;
    }

    public function setNumRef(?int $numRef): self
    {
        $this->numRef = $numRef;
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
    public function getDateReception(): ?\DateTimeImmutable
    {
        return $this->dateReception;
    }
    public function setDateReception(?\DateTimeImmutable $dateReception): static
    {
        $this->dateReception = $dateReception;
        return $this;
    }
    /**
     * @return DetailPersonnes[]
     */
    public function getDetailPersonnes(): array
    {
        return $this->detailPersonnes;
    }

    /**
     * @param DetailPersonnes[] $detailPersonnes
     */
    public function setDetailPersonnes(array $detailPersonnes): self
    {
        $this->detailPersonnes = $detailPersonnes;
        return $this;
    }
    public function toArray(array $exclude = []): array
    {
        $data = parent::toArray($exclude);

        $excludePersonne = [...$exclude, 'id', 'createdAt'];

        $data['detailPersonnes'] = array_map(
            fn (DetailPersonnes $detailPersonne) => $detailPersonne->toArray($excludePersonne),
            $this->detailPersonnes
        );

        return $data;
    }
}
