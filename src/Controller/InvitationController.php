<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Invitation;
use App\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/api/invitation')]
class InvitationController extends AbstractController
{

    /**
    Permet de créer une invitation à un événement privé localhost:8000/api/invitaion/create/1/1
     */
    #[Route('/create/{userId}/{eventId}', name:'app_invitation_create')]
    public function create(
        EntityManagerInterface $manager,
        #[MapEntity(mapping: ['userId' => 'id'])] Profile $profile,
        #[MapEntity(mapping: ['eventId' => 'id'])] Event $event){
        $invitation = new Invitation();
        $invitation->setEvent($event);
        if ($invitation->getStatus()=="cancelled"){
            return $this->json("L'événement n'accepte plus d'invitation", 400);
        }
        if ($event->getOrganistateur()->contains($this->getUser()->getProfile())){
            $invitation->setReceivedBy($profile);
            $invitation->setSentBy($profile);
            $invitation->setStatus("pending");
            $manager->persist($invitation);
            $manager->flush();
            return $this->json($invitation, 200, [], ["groups"=>"invitation:read", "user:read"]);
        }
        return $this->json("You cannot invite people to join this event", 400);
    }

    /**
     * Permet de refuser une invitation localhost:8000/api/invitation/decline/1
    */
    #[Route('/decline/{id}', name:'app_invitation_decline')]
    public function decline(Invitation $invitation, EntityManagerInterface $manager){
        if ($this->getUser()->getProfile() == $invitation->getReceivedBy() || $this->getUser()->getProfile()==$invitation->getSentBy()){
            $invitation->setStatus('declined');
            $manager->persist($invitation);
            $manager->flush();
            return $this->json("Invitation declined", 200);

        }
        return $this->json("You cannot decline others invitations", 400);
    }
}
