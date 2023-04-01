<?php

declare(strict_types=1);

namespace App\Entity;

use SimpleFW\ORM\Attribute\Column;
use SimpleFW\ORM\Attribute\Entity;
use SimpleFW\ORM\Attribute\Id;

#[Entity]
final class Team implements \JsonSerializable
{
    #[Id]
    public int $id;
    #[Column]
    public string $name;
    #[Column]
    public string $slug;
    #[Column(name: 'external_id')]
    public string $externalId;

    #[Column(name: 'sport_id')]
    public int $sportId;

    public array $players;

    public function __construct(string $name, string $slug, string $externalId, array $players)
    {
        $this->name = $name;
        $this->slug = $slug;
        $this->externalId = $externalId;
        $this->players = $players;
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

    /*
    public function getSportId(): int
    {
        return $this->sportId;
    }

    public function setSportId(int $sportId): self
    {
        $this->sportId = $sportId;

        return $this;
    }
    */

    public function getPlayers(): array
    {
        return $this->players;
    }

    public function setPlayers(array $players): self
    {
        $this->players = $players;

        return $this;
    }


    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
