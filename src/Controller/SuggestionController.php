<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Suggestion;
use App\Entity\Supported;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/suggestion')]
class SuggestionController extends AbstractController
{

    /**
     * Permet a un organisateur de créer une suggestion localhost:8000/api/suggestion/create/1
     * body:
     * {
     *     "title":"Titre de suggestion"
     * }
    */
    #[Route('/create/{id}', name: 'app_suggestion_create', methods: "POST")]
    public function create(Event $event, Request $request, SerializerInterface $serializer, EntityManagerInterface $manager){
        if ($event->getOrganistateur()->contains($this->getUser())){
            $suggestion = $serializer->deserialize($request->getContent(), Suggestion::class, 'json');
            $suggestion->setEvent($event);
            $suggestion->setStatus("to take care of");
            $manager->persist($suggestion);
            $manager->flush();
            return $this->json($suggestion, 200, [], ["groups"=>"suggestion:read"]);
        }
        return $this->json("You can't give any suggestion your opinion is not interesting.", 400);
    }


    /**
     * Permet a un utilisateur de signaler qu'il s'occupe d'un suggestion localhost:8000/api/suggestio,/takeCareOf/1
    */
    #[Route('/takeCareOf/{id}', name: 'app_suggestion_take_care_of', methods: "POST")]
    public function takeCareOf(Suggestion $suggestion, EntityManagerInterface $manager){
        if ($suggestion->getStatus()=="pris en charge"){
            return $this->json("Someone is already taking care of".$suggestion->getTitle(), 400);
        }
        $suggestion->setTakeCareBy($this->getUser()->getProfile());
        $suggestion->setStatus("pris en charge");
        $supported = new Supported();
        $supported->setSupporter($this->getUser()->getProfile());
        $supported->setEvent($suggestion->getEvent());
        $manager->persist($suggestion);
        $supported->setSuggestion($suggestion);
        $manager->persist($supported);
        $manager->flush();
        return $this->json($suggestion, 200, [], ["groups"=>"suggestion:read"]);
    }



    #[Route('/unTakeCareOf/{id}', name: 'app_suggestion_untake_care_of')]
    public function untakeCareOf(Supported $supported, EntityManagerInterface $manager){
        if ($this->getUser()->getProfile()==$supported->getSupporter()){
            $suggestion = $supported->getSuggestion();
            $suggestion->setTakeCareBy(null);
            $manager->remove($supported);
            $manager->persist($suggestion);
            $manager->flush();
            return $this->json("removed succesfully", 200);
        }
        return $this->json("You're not the one taking care of that", 400);
    }

    #[Route('/getSuggestions/{id}', name: 'app_suggestion_get_suggestions')]
    public function getSuggestions(Event $event){
        return $this->json($event->getSuggestions(), 200, [], ["groups"=>"suggestion:read"]);
    }
    #[Route('/getSupported/{id}', name: 'app_suggestion_get_supported')]
    public function getPrisEnCharge(Event $event){
        return $this->json($event->getSupporteds(), 200, [], ["groups"=>"suggestion:read"]);
    }
}
