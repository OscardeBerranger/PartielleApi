<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['event:read', 'invitation:read', 'suggestion:read'])]

    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['event:read', 'invitation:read'])]
    private ?string $lieu = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:read', 'invitation:read'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['event:read', 'invitation:read'])]
    private ?\DateTime $startOn = null;

    #[ORM\Column]
    #[Groups(['event:read', 'invitation:read'])]
    private ?\DateTime $endOn = null;

    #[ORM\ManyToMany(targetEntity: Profile::class, inversedBy: 'events')]
    #[Groups(['event:read', 'invitation:read'])]
    #[JoinTable(name: 'event_profile_organisateur')]
    private Collection $organistateur;

    #[ORM\Column(length: 255)]
    #[Groups(['event:read', 'invitation:read'])]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['event:read', 'invitation:read'])]
    private ?string $type_de_lieu = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tempStartOn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tempEndOn = null;

    #[ORM\ManyToMany(targetEntity: Profile::class, inversedBy: 'eventsAsParticipant')]
    #[Groups(['event:read', 'invitation:read'])]
    #[JoinTable(name: 'event_profile_participant')]
    private Collection $participants;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Invitation::class)]
    private Collection $invitations;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etat = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Suggestion::class)]
    private Collection $suggestions;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Supported::class)]
    private Collection $supporteds;

    public function __construct()
    {
        $this->organistateur = new ArrayCollection();
        $this->participants = new ArrayCollection();
        $this->invitations = new ArrayCollection();
        $this->suggestions = new ArrayCollection();
        $this->supporteds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStartOn(): ?\DateTime
    {
        return $this->startOn;
    }

    public function setStartOn(?\DateTime $startOn): static
    {
        $this->startOn = $startOn;

        return $this;
    }

    public function getEndOn(): ?\DateTime
    {
        return $this->endOn;
    }

    public function setEndOn(\DateTime $endOn): static
    {
        $this->endOn = $endOn;

        return $this;
    }

    /**
     * @return Collection<int, Profile>
     */
    public function getOrganistateur(): Collection
    {
        return $this->organistateur;
    }

    public function addOrganistateur(Profile $organistateur): static
    {
        if (!$this->organistateur->contains($organistateur)) {
            $this->organistateur->add($organistateur);
        }

        return $this;
    }

    public function removeOrganistateur(Profile $organistateur): static
    {
        $this->organistateur->removeElement($organistateur);

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

    public function getTypeDeLieu(): ?string
    {
        return $this->type_de_lieu;
    }

    public function setTypeDeLieu(?string $type_de_lieu): static
    {
        $this->type_de_lieu = $type_de_lieu;

        return $this;
    }

    public function getTempStartOn(): ?string
    {
        return $this->tempStartOn;
    }

    public function setTempStartOn(?string $tempStartOn): static
    {
        $this->tempStartOn = $tempStartOn;

        return $this;
    }

    public function getTempEndOn(): ?string
    {
        return $this->tempEndOn;
    }

    public function setTempEndOn(?string $tempEndOn): static
    {
        $this->tempEndOn = $tempEndOn;

        return $this;
    }

    /**
     * @return Collection<int, Profile>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Profile $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }

        return $this;
    }

    public function removeParticipant(Profile $participant): static
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getInvitations(): Collection
    {
        return $this->invitations;
    }

    public function addInvitation(Invitation $invitation): static
    {
        if (!$this->invitations->contains($invitation)) {
            $this->invitations->add($invitation);
            $invitation->setEvent($this);
        }

        return $this;
    }

    public function removeInvitation(Invitation $invitation): static
    {
        if ($this->invitations->removeElement($invitation)) {
            // set the owning side to null (unless already changed)
            if ($invitation->getEvent() === $this) {
                $invitation->setEvent(null);
            }
        }

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection<int, Suggestion>
     */
    public function getSuggestions(): Collection
    {
        return $this->suggestions;
    }

    public function addSuggestion(Suggestion $suggestion): static
    {
        if (!$this->suggestions->contains($suggestion)) {
            $this->suggestions->add($suggestion);
            $suggestion->setEvent($this);
        }

        return $this;
    }

    public function removeSuggestion(Suggestion $suggestion): static
    {
        if ($this->suggestions->removeElement($suggestion)) {
            // set the owning side to null (unless already changed)
            if ($suggestion->getEvent() === $this) {
                $suggestion->setEvent(null);
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
            $supported->setEvent($this);
        }

        return $this;
    }

    public function removeSupported(Supported $supported): static
    {
        if ($this->supporteds->removeElement($supported)) {
            // set the owning side to null (unless already changed)
            if ($supported->getEvent() === $this) {
                $supported->setEvent(null);
            }
        }

        return $this;
    }
}
