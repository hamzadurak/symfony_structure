<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Doctrine\Odm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\Hotel\HotelZoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HotelZoneRepository::class)]
#[ApiResource,
    ApiFilter(
        SearchFilter::class,
        properties: [
            'country' => SearchFilterInterface::STRATEGY_PARTIAL,
            'city' => SearchFilterInterface::STRATEGY_PARTIAL,
        ]
    ),
    ApiFilter(
        OrderFilter::class,
        properties: [
            'country',
            'city',
        ]
    )
]
class HotelZone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * country
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'validator.notBlank')]
    #[Assert\NotNull(message: 'validator.notBlank')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Your first name must be at least {{ limit }} characters long',
        maxMessage: 'Your first name cannot be longer than {{ limit }} characters',
    )]
    private ?string $country = null;

    /**
     * city
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'validator.notBlank')]
    #[Assert\NotNull(message: 'validator.notBlank')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Your first name must be at least {{ limit }} characters long',
        maxMessage: 'Your first name cannot be longer than {{ limit }} characters',
    )]
    private ?string $city = null;

    #[ORM\OneToMany(mappedBy: 'hotelZone', targetEntity: Hotel::class)]
    private Collection $hotels;

    public function __construct()
    {
        $this->hotels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection<int, Hotel>
     */
    public function getHotels(): Collection
    {
        return $this->hotels;
    }

    public function addHotel(Hotel $hotel): self
    {
        if (!$this->hotels->contains($hotel)) {
            $this->hotels->add($hotel);
            $hotel->setHotelZone($this);
        }

        return $this;
    }

    public function removeHotel(Hotel $hotel): self
    {
        if ($this->hotels->removeElement($hotel)) {
            // set the owning side to null (unless already changed)
            if ($hotel->getHotelZone() === $this) {
                $hotel->setHotelZone(null);
            }
        }

        return $this;
    }
}
