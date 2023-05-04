<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;



#[ORM\Entity(repositoryClass: EvenementRepository::class)]
#[Vich\Uploadable]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_fin = null;

    #[Vich\UploadableField(mapping: 'event_image', fileNameProperty: 'imageEv')]
    public ?File $imageFile = null;

    #[ORM\Column(length: 255)]
    private ?string $imageEv = null;

    #[ORM\Column(length: 100)]
    #[Groups("events")]
    private ?string $lieuEv = null;

    #[ORM\Column(length: 100)]
    #[Groups("events")]
    private ?string $titreEv = null;

    #[ORM\Column(length: 255)]
    #[Groups("events")]
    private ?string $DescEv = null;

    #[ORM\Column]
    private ?int $nbMax = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Reservation::class)]
    private Collection $reservations;

    #[ORM\ManyToOne(inversedBy: 'evenements')]
    private ?EventType $type = null;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getImageEv(): ?string
    {
        return $this->imageEv;
    }

    public function setImageEv(?string $imageEv): self
    {
        $this->imageEv = $imageEv;

        return $this;
    }

    public function getLieuEv(): ?string
    {
        return $this->lieuEv;
    }

    public function setLieuEv(string $lieuEv): self
    {
        $this->lieuEv = $lieuEv;

        return $this;
    }

    public function getTitreEv(): ?string
    {
        return $this->titreEv;
    }

    public function setTitreEv(string $titreEv): self
    {
        $this->titreEv = $titreEv;

        return $this;
    }

    public function getDescEv(): ?string
    {
        return $this->DescEv;
    }

    public function setDescEv(string $DescEv): self
    {
        $this->DescEv = $DescEv;

        return $this;
    }

    public function getNbMax(): ?int
    {
        return $this->nbMax;
    }

    public function setNbMax(int $nbMax): self
    {
        $this->nbMax = $nbMax;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setEvent($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getEvent() === $this) {
                $reservation->setEvent(null);
            }
        }

        return $this;
    }

    public function getType(): ?EventType
    {
        return $this->type;
    }

    public function setType(?EventType $type): self
    {
        $this->type = $type;

        return $this;
    }
}
