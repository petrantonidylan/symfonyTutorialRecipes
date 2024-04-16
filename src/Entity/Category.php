<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\Validator\BanWord;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[UniqueEntity('name')]
#[UniqueEntity('slug')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 5, groups: ['Extra'])]
    #[BanWord(groups: ['Extra'])]
    private string $name = '';

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 5)] //ne fonctionne que si c'est un string
    #[Assert\Regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')] //ne fonctionne que si c'est un string
    private string $slug = '';

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updtaedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdtaedAt(): ?\DateTimeImmutable
    {
        return $this->updtaedAt;
    }

    public function setUpdtaedAt(\DateTimeImmutable $updtaedAt): static
    {
        $this->updtaedAt = $updtaedAt;

        return $this;
    }
}
