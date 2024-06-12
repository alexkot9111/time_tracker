<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TrackerPeriodRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TrackerPeriodRepository::class)]
class TrackerPeriod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Tracker::class, inversedBy: 'trackerPeriods')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tracker $tracker = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $tracker_start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $tracker_stop = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrackerId(): ?Tracker
    {
        return $this->tracker;
    }

    public function setTrackerId(?Tracker $tracker): static
    {
        $this->tracker = $tracker;

        return $this;
    }

    public function getTrackerStart(): ?\DateTimeInterface
    {
        return $this->tracker_start;
    }

    public function setTrackerStart(\DateTimeInterface $tracker_start): static
    {
        $this->tracker_start = $tracker_start;

        return $this;
    }

    public function getTrackerStop(): ?\DateTimeInterface
    {
        return $this->tracker_stop;
    }

    public function setTrackerStop(?\DateTimeInterface $tracker_stop): static
    {
        $this->tracker_stop = $tracker_stop;

        return $this;
    }
}
