<?php

namespace App\Controller;

use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/user')]
class UserController extends AbstractController
{

    /**
     * Permet d'obtenir la liste de tout les utilisateurs localhost:8000/api/user/getUsers
    */
    #[Route('/getUsers', name: 'app_user', methods: "GET")]
    public function getAllUsers(ProfileRepository $repository): Response
    {
        return $this->json($repository->findAll(), 200, [], ["groups"=>"user:read"]);
    }

    /**
     * Permet a un utilisateur de voir ses invitations localhost:8000/api/user/getInvitations
    */
    #[Route("/getInvitations", name:'app_user_get_invitation', methods: "GET")]
    public function getInvitations(){
        return $this->json($this->getUser()->getProfile()->getInvitationsAsRecipient(), 200, [], ["groups"=>"invitation:read", "user:read"]);
    }

    /**
     * Permet a un utilisateur de voir les événement auquels il participe localhost:8000/api/user/getEvents
    */
    #[Route("/getEvents", name: "app_user_get_events", methods: "GET")]
    public function getEvents(){
        return $this->json([$this->getUser()->getProfile()->getEvents(), $this->getUser()->getProfile()->getEventsAsParticipant()], 200, [], ["groups"=>"event:read", "user:read"]);
    }
}
