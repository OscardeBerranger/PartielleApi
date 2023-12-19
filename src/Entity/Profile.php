<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
class Profile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'event:read', 'invitation:read', 'suggestion:read'])]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'profile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user:read', 'event:read', 'invitation:read'])]
    private ?User $toUser = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'event:read', 'invitation:read', 'suggestion:read'])]
    private ?string $displayName = null;

    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'organistateur')]
    private Collection $events;

    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'participants')]
    private Collection $eventsAsParticipant;

    #[ORM\OneToMany(mappedBy: 'sentBy', targetEntity: Invitation::class)]
    private Collection $invitationsAsSender;

    #[ORM\OneToMany(mappedBy: 'receivedBy', targetEntity: Invitation::class)]
    private Collection $invitationsAsRecipient;

    #[ORM\OneToMany(mappedBy: 'takeCareBy', targetEntity: Suggestion::class)]
    private Collection $takingCareOf;

    #[ORM\OneToMany(mappedBy: 'supporter', targetEntity: Supported::class)]
    private Collection $supporteds;


    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->eventsAsParticipant = new ArrayCollection();
        $this->invitationsAsSender = new ArrayCollection();
        $this->invitationsAsRecipient = new ArrayCollection();
        $this->takingCareOf = new ArrayCollection();
        $this->supporteds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToUser(): ?User
    {
        return $this->toUser;
    }

    public function setToUser(User $toUser): static
    {
        $this->toUser = $toUser;

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): static
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->addOrganistateur($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            $event->removeOrganistateur($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEventsAsParticipant(): Collection
    {
        return $this->eventsAsParticipant;
    }

    public function addEventsAsParticipant(Event $eventsAsParticipant): static
    {
        if (!$this->eventsAsParticipant->contains($eventsAsParticipant)) {
            $this->eventsAsParticipant->add($eventsAsParticipant);
            $eventsAsParticipant->addParticipant($this);
        }

        return $this;
    }

    public function removeEventsAsParticipant(Event $eventsAsParticipant): static
    {
        if ($this->eventsAsParticipant->removeElement($eventsAsParticipant)) {
            $eventsAsParticipant->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getInvitationsAsSender(): Collection
    {
        return $this->invitationsAsSender;
    }

    public function addInvitationsAsSender(Invitation $invitationsAsSender): static
    {
        if (!$this->invitationsAsSender->contains($invitationsAsSender)) {
            $this->invitationsAsSender->add($invitationsAsSender);
            $invitationsAsSender->setSentBy($this);
        }

        return $this;
    }

    public function removeInvitationsAsSender(Invitation $invitationsAsSender): static
    {
        if ($this->invitationsAsSender->removeElement($invitationsAsSender)) {
            // set the owning side to null (unless already changed)
            if ($invitationsAsSender->getSentBy() === $this) {
                $invitationsAsSender->setSentBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getInvitationsAsRecipient(): Collection
    {
        return $this->invitationsAsRecipient;
    }

    public function addInvitationsAsRecipient(Invitation $invitationsAsRecipient): static
    {
        if (!$this->invitationsAsRecipient->contains($invitationsAsRecipient)) {
            $this->invitationsAsRecipient->add($invitationsAsRecipient);
            $invitationsAsRecipient->setReceivedBy($this);
        }

        return $this;
    }

    public function removeInvitationsAsRecipient(Invitation $invitationsAsRecipient): static
    {
        if ($this->invitationsAsRecipient->removeElement($invitationsAsRecipient)) {
            // set the owning side to null (unless already changed)
            if ($invitationsAsRecipient->getReceivedBy() === $this) {
                $invitationsAsRecipient->setReceivedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Suggestion>
     */
    public function getTakingCareOf(): Collection
    {
        return $this->takingCareOf;
    }

    public function addTakingCareOf(Suggestion $takingCareOf): static
    {
        if (!$this->takingCareOf->contains($takingCareOf)) {
            $this->takingCareOf->add($takingCareOf);
            $takingCareOf->setTakeCareBy($this);
        }

        return $this;
    }

    public function removeTakingCareOf(Suggestion $takingCareOf): static
    {
        if ($this->takingCareOf->removeElement($takingCareOf)) {
            // set the owning side to null (unless already changed)
            if ($takingCareOf->getTakeCareBy() === $this) {
                $takingCareOf->setTakeCareBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Supported>
     */
    public function getSupporteds(): Collection
    {
        return $this->supporteds;
    }

    public function addSupported(Supported $supported): static
    {
        if (!$this->supporteds->contains($supported)) {
            $this->supporteds->add($supported);
            $supported->setSupporter($this);
        }

        return $this;
    }

    public function removeSupported(Supported $supported): static
    {
        if ($this->supporteds->removeElement($supported)) {
            // set the owning side to null (unless already changed)
            if ($supported->getSupporter() === $this) {
                $supported->setSupporter(null);
            }
        }

        return $this;
    }

}
