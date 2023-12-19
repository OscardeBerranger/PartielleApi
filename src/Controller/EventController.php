<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Invitation;
use App\Repository\EventRepository;
use App\Repository\InvitationRepository;
use App\Services\EventServices;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route("/api/event")]
class EventController extends AbstractController
{
    #[Route('/', name: 'app_event')]
    public function index(): Response
    {
        return $this->render('event/index.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }


    /**
    Récupère la liste de tout les événements public localhost:8000/api/event/getPublicEvents
     */
    #[Route('/getPublicEvents', name:'app_event_public_get', methods: 'GET')]
    public function getPublicEvents(EventRepository $repository){
        return $this->json($repository->findBy(["status"=>"public"]), 200, [], ["groups"=>"event:read", "user:read"]);
    }

    /**
    Récupère la liste de toutes les suggestions d'un événement localhost:8000/api/event/getEventSuggestion/12
     */
    #[Route("/getEventSuggestions/{id}", name:"app_event_suggestion_get", methods: "GET")]
    public function getEventSuggestions(Event $event){
        return $this->json($event->getSuggestions(), 200, [], ["groups"=>"suggestion:read"]);
    }

    /**
    Récupère le statut d'un événement précis localhost:8000/api/event/status/12
     */
    #[Route("/status/{id}", name:"app_event_status", methods:"GET")]
    public function getEventStatus(Event $event){
        return $this->json($event->getStatus(), 200);
    }

    /**
    Récupère la liste des invitations triés par status (accepté, refusé, en attente) localhost:8000/api/event/getUserList/12
     */
    #[Route("/getUserList/{id}", name:"app_event_get_user_list", methods: "GET")]
    public function getUserList(Event $event){
        if (!$event->getParticipants()->contains($this->getUser()) && !$event->getOrganistateur()->contains($this->getUser())){
            return $this->json("You are not participating to this event.", 200);
        }
        $inv = $event->getInvitations();
        $pending = [];
        $accepted = [];
        $declined = [];
        foreach ($inv as $item){
            if ($item->getStatus()=="declined"){
                $declined[]=$item;
            }elseif ($item->getStatus()=="accepted"){
                $accepted[]=$item;
            }elseif ($item->getStatus()=="pending"){
                $pending[]=$item;
            }
        }
        $returning = [
            "accepted"=>$accepted,
            "declined"=>$declined,
            "pending"=>$pending
        ];
        return $this->json($returning, 200, [], ["groups"=>"invitation:read"]);
    }

    /**
     * Créer un événement public ou privé localhost:8000/api/event/create
     * body :
     * {
     *     "lieu":"ah",
     *     "description":"ah",
     *     "tempStartOn":"2023-12-19",
     *     "tempEndOn":"2023-12-20",
     *     "status":"private",
     *     "type_de_lieu":"public"
     * }
     */
    #[Route('/create', name: 'app_event_create', methods: 'POST')]
    public function create(EntityManagerInterface $manager, EventServices $eventServices, Request $request, SerializerInterface $serializer){
        $newEvent = $serializer->deserialize($request->getContent(), Event::class, 'json');
        $newEvent->setStartOn(new \DateTime($newEvent->getTempStartOn()));
        $newEvent->setEndOn(new \DateTime($newEvent->getTempEndOn()));
        if (!$eventServices->startDateIsGood($newEvent->getStartOn())){
            return $this->json('An error as occured apparently the start date you entered : '.$newEvent->getStartOn()->format('Y-m-d H:i:s')." as already passed.", 400);
        }
        if (!$eventServices->endDateIsGood($newEvent->getStartOn(), $newEvent->getEndOn())){
            return $this->json('An error as occured apparently the end date you entered : '.$newEvent->getEndOn()->format('Y-m-d H:i:s')." is occuring before your start date : ".$newEvent->getStartOn()->format('Y-m-d H:i:s'), 400);
        }
        $newEvent->addOrganistateur($this->getUser()->getProfile());
        if ($newEvent->getStatus()){
            $newEvent->setStatus(strtolower($newEvent->getStatus()));
        }else{
            $newEvent->setStatus('private');
        }
        $newEvent->setStatus('onSchedule');
        $manager->persist($newEvent);
        $manager->flush();
        return $this->json($newEvent, 200, [], ["groups"=>"event:read", "user:read"]);
    }

    /**
     * Permet de participer à un événement public localhost:8000/api/event/participate/1
    */
    #[Route('/participate/{id}', name: 'app_event_participate', methods: 'GET')]
    public function participate(Event $event, EventServices $services, EntityManagerInterface $manager){
        if ($event->getStatus()=="public"){
            if (!$services->isParticipant($event, $this->getUser()->getProfile())){
                $event->addParticipant($this->getUser()->getProfile());
                $manager->persist($event);
                $manager->flush();
                return $this->json($event, 200, [], ["groups"=>"event:read", "user:read"]);
            }
            return $this->json("You are already part of this event.", 400);
        }
        return $this->json("You can't join this event.", 400);
    }

    /**
     * permet de récupérer la liste des événements auquels participe un utilisateur
    */
    #[Route("/getmyevents", name:"app_event_get_mine", methods: "DELETE")]
    public function getEventFromUser(){
        return $this->json($this->getUser()->getProfile()->getEvents(), 200, [], ["groups"=>"event:read", "user:read"]);
    }


    /**
     * Permet de modifier la date de début d'un événement
    */
    #[Route("/updateStartDate/{id}", name:"app_event_update_start_date", methods: "POST")]
    public function updateStartDate(Event $event, EntityManagerInterface $manager, Request $request, SerializerInterface $serializer){
        $temp = $serializer->deserialize($request->getContent(), Event::class, 'json');
        $event->setStartOn(new \DateTime($temp->getTempStartOn()));
        $manager->persist($event);
        $manager->flush();
        return $this->json($event, 200, [], ["groups"=>"event:read"]);
    }

    /**
     * Permet de modifier la date de fin d'un événement
    */
    #[Route("/updateEndDate/{id}", name:"app_event_update_end_date", methods: "POST")]
    public function updateEndDate(Event $event, EntityManagerInterface $manager, Request $request, SerializerInterface $serializer){
        $temp = $serializer->deserialize($request->getContent(), Event::class, 'json');
        $event->setEndOn(new \DateTime($temp->getTempStartOn()));
        $manager->persist($event);
        $manager->flush();
        return $this->json($event, 200, [], ["groups"=>"event:read"]);
    }

}
