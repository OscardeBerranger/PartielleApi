<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Profile;
use App\Repository\EventRepository;
use App\Repository\InvitationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/api/private/event')]
class PrivateEventController extends AbstractController
{

    /**
     * Permet de récupérerla liste des événements privés localhost:8000/api/private/event/get
    */
    #[Route('/get', name:'app_event_private_get', methods: 'GET')]
    public function getPrivateEvents(EventRepository $repository) :Response{
        return $this->json($repository->findBy(["status"=>"private"]), 200, [], ["groups"=>"event:read", "user:read"]);
    }

    /**
     * Permet d'accepter une invitation à un événement localhost:8000/api/private/event/participate/1
    */
    #[Route('/participate/{invitationId}', name: 'app_event_participate_to_private', methods: 'POST')]
    public function participate($invitationId, EntityManagerInterface $manager, InvitationRepository $repository) :Response{
        $invitation = $repository->findOneBy(["id"=>$invitationId]);
        if ($invitation->getReceivedBy()!==$this->getUser()->getProfile()){
            return $this->json("You can't accept for others", 400);
        }
        if ($invitation->getEvent()->getStartOn() == new \DateTime()){
            return $this->json("The even alrady started", 400);
        }
        $invitation = $repository->findOneBy(["id"=>$invitationId]);
        $event = $invitation->getEvent();
        $event->addParticipant($this->getUser()->getProfile());
        $invitation->setStatus("accepted");
        $manager->persist($invitation);
        $manager->persist($event);
        $manager->flush();
        return $this->json($event, 200, [], ["groups"=>'event:read', "user:read"]);
    }


    #[Route('/promote/{userId}/{eventId}', name: 'app_private_event_promote')]
    public function promote(
        EntityManagerInterface $manager,
        #[MapEntity(mapping: ['userId' => 'id'])] Profile $profile,
        #[MapEntity(mapping: ['eventId' => 'id'])] Event $event,
    ){
        if (!$event->getOrganistateur()->contains($this->getUser()->getProfile())){
            return $this->json("You cannot promote people", 400);
        }
        if ($event->getOrganistateur()->contains($profile)){
            return $this->json("this user is already an admin", 400);
        }
        if (!$event->getParticipants()->contains($profile)&&!$event->getParticipants()->contains($profile)){
            return $this->json("this user is not part of the event", 400);
        }
        $event->removeParticipant($profile);
        $event->addOrganistateur($profile);
        $manager->persist($event);
        $manager->flush();
        return $this->json($event, 200, [], ["groups"=>"event:read", "user:read"]);
    }

    #[Route('/demote/{userId}/{eventId}', name: 'app_private_event_demote')]
    public function demote(
        EntityManagerInterface $manager,
        #[MapEntity(mapping: ['userId' => 'id'])] Profile $profile,
        #[MapEntity(mapping: ['eventId' => 'id'])] Event $event,
    ){
        if (!$event->getOrganistateur()->contains($this->getUser()->getProfile())){
            return $this->json("You cannot demote people", 400);
        }
        if (!$event->getOrganistateur()->contains($profile)){
            return $this->json("this user is already not an admin", 400);
        }
        if (!$event->getParticipants()->contains($profile)&&!$event->getOrganistateur()->contains($profile)){
            return $this->json("this user is not part of the event", 400);
        }
        $event->removeOrganistateur($profile);
        $event->addParticipant($profile);
        $manager->persist($event);
        $manager->flush();
        return $this->json($event, 200, [], ["groups"=>"event:read", "user:read"]);
    }
}
