<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TrackerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TrackerRepository::class)]
class Tracker
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'trackers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'trackers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company = null;

    #[Assert\NotBlank, Assert\NotNull]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created = null;

    /**
     * @var Collection<int, TrackerPeriod>
     */
    #[ORM\OneToMany(targetEntity: TrackerPeriod::class, mappedBy: 'tracker', orphanRemoval: true)]
    private Collection $trackerPeriods;

    public function __construct()
    {
        $this->trackerPeriods = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->user;
    }

    public function setUserId(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCompanyId(): ?Company
    {
        return $this->company;
    }

    public function setCompanyId(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): static
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return Collection<int, TrackerPeriod>
     */
    public function getTrackerPeriods(): Collection
    {
        return $this->trackerPeriods;
    }

    public function addTrackerPeriod(TrackerPeriod $trackerPeriod): static
    {
        if (!$this->trackerPeriods->contains($trackerPeriod)) {
            $this->trackerPeriods->add($trackerPeriod);
            $trackerPeriod->setTrackerId($this);
        }

        return $this;
    }

    public function removeTrackerPeriod(TrackerPeriod $trackerPeriod): static
    {
        if ($this->trackerPeriods->removeElement($trackerPeriod)) {
            // set the owning side to null (unless already changed)
            if ($trackerPeriod->getTrackerId() === $this) {
                $trackerPeriod->setTrackerId(null);
            }
        }

        return $this;
    }
}
