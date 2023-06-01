<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $manager_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $venue = null;

    #[ORM\Column]
    private ?int $external_id = null;

    #[ORM\Column]
    private ?int $sport_id = null;

    #[ORM\Column]
    private ?int $country_id = null;

    private array $players = [];

    private array $events = [];

    private array $tournaments = [];

    public function __construct(string $name, ?string $manager_name, ?string $venue, int $external_id)
    {
        $this->name = $name;
        $this->manager_name = $manager_name;
        $this->venue = $venue;
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

    public function getManagerName(): ?string
    {
        return $this->manager_name;
    }

    public function setManagerName(?string $manager_name): self
    {
        $this->manager_name = $manager_name;

        return $this;
    }

    public function getVenue(): ?string
    {
        return $this->venue;
    }

    public function setVenue(?string $venue): self
    {
        $this->venue = $venue;

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

    public function getCountryId(): ?int
    {
        return $this->country_id;
    }

    public function setCountryId(int $country_id): self
    {
        $this->country_id = $country_id;

        return $this;
    }

    public function getPlayers(): array
    {
        return $this->players;
    }

    public function setPlayers(array $players): self
    {
        $this->players = $players;

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

    public function getTournaments(): array
    {
        return $this->tournaments;
    }

    public function setTournaments(array $tournaments): self
    {
        $this->tournaments = $tournaments;

        return $this;
    }
}
