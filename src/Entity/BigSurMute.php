<?php

namespace App\Entity;

use App\Repository\BigSurMuteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BigSurMuteRepository::class)]
class BigSurMute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $follower = null;

    #[ORM\Column(length: 50)]
    private ?string $following = null;

    #[ORM\Column]
    private ?int $state = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFollower(): ?string
    {
        return $this->follower;
    }

    public function setFollower(string $follower): self
    {
        $this->follower = $follower;

        return $this;
    }

    public function getFollowing(): ?string
    {
        return $this->following;
    }

    public function setFollowing(string $following): self
    {
        $this->following = $following;

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
