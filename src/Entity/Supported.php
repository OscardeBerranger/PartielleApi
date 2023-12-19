<?php

namespace App\Entity;

use App\Repository\SupportedRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SupportedRepository::class)]
class Supported
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'supporteds')]
    private ?Event $event = null;

    #[ORM\ManyToOne(inversedBy: 'supporteds')]
    private ?Profile $supporter = null;

    #[ORM\OneToOne(inversedBy: 'supported', cascade: ['persist', 'remove'])]
    private ?Suggestion $suggestion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getSupporter(): ?Profile
    {
        return $this->supporter;
    }

    public function setSupporter(?Profile $supporter): static
    {
        $this->supporter = $supporter;

        return $this;
    }

    public function getSuggestion(): ?Suggestion
    {
        return $this->suggestion;
    }

    public function setSuggestion(?Suggestion $suggestion): static
    {
        $this->suggestion = $suggestion;

        return $this;
    }
}
