<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $position = null;

    #[ORM\Column(length: 255)]
    private ?string $date_of_birth = null;

    #[ORM\Column]
    private ?int $external_id = null;

    #[ORM\Column]
    private ?int $sport_id = null;

    #[ORM\Column]
    private ?int $team_id = null;

    #[ORM\Column]
    private ?int $country_id = null;

    private array $events = [];

    public function __construct(string $name, string $slug, ?string $position, ?string $date_of_birth, int $external_id)
    {
        $this->name = $name;
        $this->slug = $slug;
        $this->position = $position;
        $this->date_of_birth = $date_of_birth;
        $this->external_id = $external_id;
    }

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getDateOfBirth(): ?string
    {
        return $this->date_of_birth;
    }

    public function setDateOfBirth(?string $date_of_birth): self
    {
        $this->date_of_birth = $date_of_birth;

        return $this;
    }

    public function getExternalId(): ?int
    {
        return $this->external_id;
    }

    public function setExternalId(int $external_id): self
    {
        $this->external_id = $external_id;

        return $this;
    }

    public function getSportId(): ?int
    {
        return $this->sport_id;
    }

    public function setSportId(int $sport_id): self
    {
        $this->sport_id = $sport_id;

        return $this;
    }

    public function getTeamId(): ?int
    {
        return $this->team_id;
    }

    public function setTeamId(int $team_id): self
    {
        $this->team_id = $team_id;

        return $this;
    }

    public function getCountryId(): ?int
    {
        return $this->country_id;
    }

    public function setCountryId(int $country_id): self
    {
        $this->country_id = $country_id;

        return $this;
    }

    public function getEvents(): array
    {
        return $this->events;
    }

    public function setEvents(array $events): self
    {
        $this->events = $events;

        return $this;
    }
}
