<?php

namespace App\Entity;

use App\Repository\InvitationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: InvitationRepository::class)]
class Invitation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'invitationsAsSender')]
    #[Groups('invitation:read')]
    private ?Profile $sentBy = null;

    #[ORM\ManyToOne(inversedBy: 'invitationsAsRecipient')]
    #[Groups('invitation:read')]
    private ?Profile $receivedBy = null;

    #[ORM\ManyToOne(inversedBy: 'invitations')]
    #[Groups('invitation:read')]
    private ?Event $event = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSentBy(): ?Profile
    {
        return $this->sentBy;
    }

    public function setSentBy(?Profile $sentBy): static
    {
        $this->sentBy = $sentBy;

        return $this;
    }

    public function getReceivedBy(): ?Profile
    {
        return $this->receivedBy;
    }

    public function setReceivedBy(?Profile $receivedBy): static
    {
        $this->receivedBy = $receivedBy;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
