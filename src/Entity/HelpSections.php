<?php

namespace App\Entity;

use App\Repository\HelpSectionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HelpSectionsRepository::class)]
class HelpSections
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $section_id = null;

    #[ORM\Column(length: 50)]
    private ?string $section_name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSectionId(): ?string
    {
        return $this->section_id;
    }

    public function setSectionId(string $section_id): self
    {
        $this->section_id = $section_id;

        return $this;
    }

    public function getSectionName(): ?string
    {
        return $this->section_name;
    }

    public function setSectionName(string $section_name): self
    {
        $this->section_name = $section_name;

        return $this;
    }
}
