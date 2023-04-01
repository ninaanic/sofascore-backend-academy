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
    private int $id;
    #[Column]
    private string $name;
    #[Column]
    private string $slug;
    #[Column(name: 'external_id')]
    private string $externalId;

    public function __construct(string $name, string $slug, string $externalId)
    {
        $this->name = $name;
        $this->slug = $slug;
        $this->externalId = $externalId;
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


    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
