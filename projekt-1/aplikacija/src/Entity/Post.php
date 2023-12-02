<?php

declare(strict_types=1);

namespace App\Entity;

use SimpleFW\ORM\Attribute\Column;
use SimpleFW\ORM\Attribute\Entity;
use SimpleFW\ORM\Attribute\Id;

#[Entity]
final class Post implements \JsonSerializable
{
    #[Id]
    private int $id;
    #[Column]
    private string $title;
    #[Column]
    private string $text;
    #[Column]
    private string $status;
    #[Column(name: 'date_created')]
    private string $dateCreated;

    public function __construct(string $title, string $text, ?PostStatusEnum $status = null)
    {
        $this->title = $title;
        $this->text = $text;
        $this->setStatus($status ?? PostStatusEnum::Draft);
        $this->setDateCreated(new \DateTimeImmutable());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getStatus(): PostStatusEnum
    {
        return PostStatusEnum::from($this->status);
    }

    public function setStatus(PostStatusEnum $status): self
    {
        $this->status = $status->value;

        return $this;
    }

    public function getDateCreated(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->dateCreated);
    }

    public function setDateCreated(\DateTimeImmutable $dateCreated): self
    {
        $this->dateCreated = $dateCreated->format(\DateTimeInterface::ATOM);

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
