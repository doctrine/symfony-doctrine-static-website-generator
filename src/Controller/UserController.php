<?php

namespace App\Controller;

use App\Models\User;
use App\Repositories\UserRepository;
use Doctrine\StaticWebsiteGenerator\Controller\Response;

class UserController
{
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index() : Response
    {
        $users = $this->userRepository->findAll();

        return new Response([
            'users' => $users
        ]);
    }

    public function user(string $username) : Response
    {
        $user = $this->userRepository->findOneByUsername($username);

        return new Response([
            'user' => $user,
        ], '/user.html.twig');
    }
}
