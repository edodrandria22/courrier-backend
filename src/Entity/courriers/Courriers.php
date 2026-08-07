<?php

namespace App\Entity\courriers;

use App\Entity\courriers\BaseCourriers;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\courriers\CourriersRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: CourriersRepository::class)]
class Courriers extends BaseCourriers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function setId(?int $id): static
    {
        $this->id = $id;
        return $this;
    }
    #[ORM\OneToMany(mappedBy: 'courrier', targetEntity: DetailPersonnes::class, cascade: ['persist', 'remove'])]
    private Collection $detailPersonnes;

    public function __construct()
    {
        $this->detailPersonnes = new ArrayCollection();
    }

    /**
     * @return Collection<int, DetailPersonnes>
     */
    public function getDetailPersonnes(): Collection
    {
        return $this->detailPersonnes;
    }

    public function addDetailPersonne(DetailPersonnes $detailPersonne): self
    {
        if (!$this->detailPersonnes->contains($detailPersonne)) {
            $this->detailPersonnes->add($detailPersonne);
            $detailPersonne->setCourrier($this);
        }

        return $this;
    }

    public function removeDetailPersonne(DetailPersonnes $detailPersonne): self
    {
        if ($this->detailPersonnes->removeElement($detailPersonne)) {
            // set the owning side to null (unless already changed)
            if ($detailPersonne->getCourrier() === $this) {
                $detailPersonne->setCourrier(null);
            }
        }
        return $this;
    }
}
