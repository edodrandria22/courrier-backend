<?php

namespace App\Entity\courriers;

use App\Entity\courriers\BaseCourriers;
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
}
