<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    //#[ORM\Column]
    //private ?int $external_id = null;

    public function __construct(string $name) 
    {
        $this->name = $name;
        //$this->external_id = $external_id;
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

    /*
    public function getExternalId(): ?int
    {
        return $this->external_id;
    }

    public function setExternalId(int $external_id): self
    {
        $this->external_id = $external_id;

        return $this;
    }
    */
}
