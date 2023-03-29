<?php

namespace App\Entity;

use App\Repository\UserVisitorRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserVisitorRepository::class)]
class UserVisitor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $v_id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $v_name = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $v_about = null;

    #[ORM\Column(nullable: true)]
    private ?int $visits = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVId(): ?string
    {
        return $this->v_id;
    }

    public function setVId(string $v_id): self
    {
        $this->v_id = $v_id;

        return $this;
    }

    public function getVName(): ?string
    {
        return $this->v_name;
    }

    public function setVName(?string $v_name): self
    {
        $this->v_name = $v_name;

        return $this;
    }

    public function getVAbout(): ?string
    {
        return $this->v_about;
    }

    public function setVAbout(?string $v_about): self
    {
        $this->v_about = $v_about;

        return $this;
    }

    public function getVisits(): ?int
    {
        return $this->visits;
    }

    public function setVisits(?int $visits): self
    {
        $this->visits = $visits;

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
