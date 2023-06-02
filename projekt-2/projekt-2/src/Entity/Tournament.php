<?php

namespace App\Entity;

use App\Repository\TournamentRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Sport;
use App\Entity\Country;

#[ORM\Entity(repositoryClass: TournamentRepository::class)]
class Tournament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?int $number_of_competitors = null;

    #[ORM\Column]
    private ?int $head_to_head_count = null;

    #[ORM\Column]
    private ?int $external_id = null;

    #[ORM\Column]
    private ?int $sport_id = null;

    #[ORM\Column]
    private ?int $country_id = null;

    //public Sport $sport;
    //public Country $country;

    public function __construct(string $name, string $slug, int $external_id, ?int $number_of_competitors, ?int $head_to_head_count)
    {
        $this->name = $name;
        $this->slug = $slug;
        $this->external_id = $external_id;
        $this->number_of_competitors = $number_of_competitors;
        $this->head_to_head_count = $head_to_head_count;
       // $this->sport = $sport;
       // $this->country = $country;
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

    public function getExternalId(): ?int
    {
        return $this->external_id;
    }

    public function setExternalId(int $external_id): self
    {
        $this->external_id = $external_id;

        return $this;
    }

    public function getNumberOfCompetitors(): ?int
    {
        return $this->number_of_competitors;
    }

    public function setNumberOfCompetitors(?int $number_of_competitors): self
    {
        $this->number_of_competitors = $number_of_competitors;

        return $this;
    }

    public function getHeadToHeadCount(): ?int
    {
        return $this->head_to_head_count;
    }

    public function setHeadToHeadCount(?int $head_to_head_count): self
    {
        $this->head_to_head_count = $head_to_head_count;

        return $this;
    }

    public function getSportId(): int
    {
        return $this->sport_id;
    }

    public function setSportId(int $sport_id): self
    {
        $this->sport_id = $sport_id;

        return $this;
    }

    public function getCountryId(): int
    {
        return $this->country_id;
    }

    public function setCountryId(int $country_id): self
    {
        $this->country_id = $country_id;

        return $this;
    }

    /*

    public function getSport(): Sport
    {
        return $this->sport;
    }

    public function setSport(Sport $sport): self
    {
        $this->sport = $sport;

        return $this;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): self
    {
        $this->country = $country;

        return $this;
    }
    */
}
