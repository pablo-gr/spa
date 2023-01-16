<?php

namespace App\Entity;

use App\Repository\SchedulerRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SchedulerRepository::class)]
class Scheduler
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'schedulers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?service $service = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $start_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $end_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getService(): ?service
    {
        return $this->service;
    }

    public function setService(?service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getStartAt(): ?DateTimeInterface
    {
        return $this->start_at;
    }

    public function setStartAt(DateTimeInterface $start_at): self
    {
        $this->start_at = $start_at;

        return $this;
    }

    public function getEndAt(): ?DateTimeInterface
    {
        return $this->end_at;
    }

    public function setEndAt(DateTimeInterface $end_at): self
    {
        $this->end_at = $end_at;

        return $this;
    }
}
