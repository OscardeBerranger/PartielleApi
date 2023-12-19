<?php

namespace App\Services;

use App\Entity\Event;

class EventServices
{
    public function startDateIsGood($date){
        return $date > new \DateTimeImmutable();
    }
    public function endDateIsGood($sDate, $eDate){
        return $sDate<$eDate;
    }
    public function isParticipant($event, $profile){
        return $event->getParticipants()->contains($profile);
    }
}