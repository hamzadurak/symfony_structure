<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\Page\PageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ApiResource]
class Page
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?int $starCount = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $slug = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Hotel $hotel = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?HotelZone $zone = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?HotelCategory $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getStarCount(): ?int
    {
        return $this->starCount;
    }

    public function setStarCount(?int $starCount): self
    {
        $this->starCount = $starCount;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getHotel(): ?Hotel
    {
        return $this->hotel;
    }

    public function setHotel(?Hotel $hotel): self
    {
        $this->hotel = $hotel;

        return $this;
    }

    public function getZone(): ?HotelZone
    {
        return $this->zone;
    }

    public function setZone(?HotelZone $zone): self
    {
        $this->zone = $zone;

        return $this;
    }

    public function getCategory(): ?HotelCategory
    {
        return $this->category;
    }

    public function setCategory(?HotelCategory $category): self
    {
        $this->category = $category;

        return $this;
    }
}
