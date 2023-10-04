<?php

namespace App\Entity;

use App\Repository\ScoreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScoreRepository::class)]
class Score
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $total = null;

    #[ORM\Column(nullable: true)]
    private ?int $period1 = null;

    #[ORM\Column(nullable: true)]
    private ?int $period2 = null;

    #[ORM\Column(nullable: true)]
    private ?int $period3 = null;

    #[ORM\Column(nullable: true)]
    private ?int $period4 = null;

    #[ORM\Column(nullable: true)]
    private ?int $overtime = null;

    public function __construct(?int $total, ?int $period1, ?int $period2, ?int $period3, ?int $period4, ?int $overtime)
    {
        $this->total = $total;
        $this->period1 = $period1;
        $this->period2 = $period2;
        $this->period3 = $period3;
        $this->period4 = $period4;
        $this->overtime = $overtime;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(?int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getPeriod1(): ?int
    {
        return $this->period1;
    }

    public function setPeriod1(?int $period1): self
    {
        $this->period1 = $period1;

        return $this;
    }

    public function getPeriod2(): ?int
    {
        return $this->period2;
    }

    public function setPeriod2(?int $period2): self
    {
        $this->period2 = $period2;

        return $this;
    }

    public function getPeriod3(): ?int
    {
        return $this->period3;
    }

    public function setPeriod3(?int $period3): self
    {
        $this->period3 = $period3;

        return $this;
    }

    public function getPeriod4(): ?int
    {
        return $this->period4;
    }

    public function setPeriod4(?int $period4): self
    {
        $this->period4 = $period4;

        return $this;
    }

    public function getOvertime(): ?int
    {
        return $this->overtime;
    }

    public function setOvertime(?int $overtime): self
    {
        $this->overtime = $overtime;

        return $this;
    }
}
