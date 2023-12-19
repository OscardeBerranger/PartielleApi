<?php

namespace App\Services;

use App\Repository\UserRepository;

class UserServices
{
    private $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function isValid($email){
        return $this->userRepository->findOneBy(['email'=>$email]);
    }
}