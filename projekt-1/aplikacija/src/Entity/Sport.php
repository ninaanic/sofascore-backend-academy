<?php

declare(strict_types=1);

namespace App\Entity;

use SimpleFW\ORM\Attribute\Column;
use SimpleFW\ORM\Attribute\Entity;
use SimpleFW\ORM\Attribute\Id;

#[Entity]
final class Sport implements \JsonSerializable
{
    #[Id]
    public int $id;
    #[Column]
    public string $name;
    #[Column]
    public string $slug;
    #[Column(name: 'external_id')]
    public string $externalId;

    public array $tournaments;
    public array $teams;

    public function __construct(string $name, string $slug, string $externalId, array $tournaments, array $teams)
    {
        $this->name = $name;
        $this->slug = $slug;
        $this->externalId = $externalId;
        $this->tournaments = $tournaments;
        $this->teams = $teams;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;

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

    public function getTeams(): array
    {
        return $this->teams;
    }

    public function setTeams(array $teams): self
    {
        $this->teams = $teams;

        return $this;
    }


    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
