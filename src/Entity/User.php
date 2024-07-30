<?php

declare(strict_types=1);

namespace App\Entity;

use App\Config\UserRole;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user'])]
    private ?Company $company = null;

    #[Assert\NotBlank, Assert\NotNull, Assert\Email(
        message: 'The email "{{ value }}" is not valid.'
    )]
    #[ORM\Column(length: 255)]
    #[Groups(['user'])]
    private ?string $email = null;

    #[Assert\NotBlank, Assert\NotNull]
    #[ORM\Column(length: 255)]
    #[Groups(['user'])]
    private ?string $first_name = null;

    #[Assert\NotBlank, Assert\NotNull]
    #[ORM\Column(length: 255)]
    #[Groups(['user'])]
    private ?string $last_name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['user'])]
    private ?\DateTimeInterface $created = null;

    /**
     * @var Collection<int, Tracker>
     */
    #[ORM\OneToMany(targetEntity: Tracker::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $trackers;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user'])]
    private ?string $password = null;

    #[ORM\Column(type: 'string', enumType: UserRole::class)]
    #[Groups(['user'])]
    private ?string $role;

    public function __construct()
    {
        $this->trackers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

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
     * @return Collection<int, Tracker>
     */
    public function getTrackers(): Collection
    {
        return $this->trackers;
    }

    public function addTracker(Tracker $tracker): static
    {
        if (!$this->trackers->contains($tracker)) {
            $this->trackers->add($tracker);
            $tracker->setUserId($this);
        }

        return $this;
    }

    public function removeTracker(Tracker $tracker): static
    {
        if ($this->trackers->removeElement($tracker)) {
            // set the owning side to null (unless already changed)
            if ($tracker->getUserId() === $this) {
                $tracker->setUserId(null);
            }
        }

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }

    public function setRole(?string $role): static
    {
        $this->role = $role;

        return $this;
    }
}
