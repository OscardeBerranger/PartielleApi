<?php

namespace App\Entity;

use App\Repository\SuggestionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SuggestionRepository::class)]
class Suggestion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('suggestion:read')]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('suggestion:read')]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('suggestion:read')]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'takingCareOf')]
    #[Groups('suggestion:read')]
    private ?Profile $takeCareBy = null;

    #[ORM\ManyToOne(inversedBy: 'suggestions')]
    #[Groups('suggestion:read')]
    private ?Event $event = null;

    #[ORM\OneToOne(mappedBy: 'suggestion', cascade: ['persist', 'remove'])]
    private ?Supported $supported = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTakeCareBy(): ?Profile
    {
        return $this->takeCareBy;
    }

    public function setTakeCareBy(?Profile $takeCareBy): static
    {
        $this->takeCareBy = $takeCareBy;

        return $this;
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

    public function getSupported(): ?Supported
    {
        return $this->supported;
    }

    public function setSupported(?Supported $supported): static
    {
        // unset the owning side of the relation if necessary
        if ($supported === null && $this->supported !== null) {
            $this->supported->setSuggestion(null);
        }

        // set the owning side of the relation if necessary
        if ($supported !== null && $supported->getSuggestion() !== $this) {
            $supported->setSuggestion($this);
        }

        $this->supported = $supported;

        return $this;
    }
}
