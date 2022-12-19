<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\Hotel\HotelRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HotelRepository::class)]
#[ApiResource]
class Hotel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $starCount = null;

    #[ORM\ManyToOne(inversedBy: 'hotels')]
    private ?HotelZone $hotelZone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getHotelZone(): ?HotelZone
    {
        return $this->hotelZone;
    }

    public function setHotelZone(?HotelZone $hotelZone): self
    {
        $this->hotelZone = $hotelZone;

        return $this;
    }
}
