<?php

declare(strict_types=1);

namespace App\Requests;

use App\Models\User;
use App\Repositories\UserRepository;
use Doctrine\StaticWebsiteGenerator\Request\ArrayRequestCollection;
use Doctrine\StaticWebsiteGenerator\Request\RequestCollection;

class UserRequests
{
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUsers() : RequestCollection
    {
        /** @var User[] $users */
        $users = $this->userRepository->findAll();

        $requests = [];

        foreach ($users as $user) {
            $requests[] = [
                'username' => $user->getUsername(),
            ];
        }

        return new ArrayRequestCollection($requests);
    }
}
