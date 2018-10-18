<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Doctrine\SkeletonMapper\ObjectRepository\BasicObjectRepository;

class UserRepository extends BasicObjectRepository
{
    public function findOneByUsername(string $username) : User
    {
        /** @var User $user */
        $user = $this->findOneBy(['username' => $username]);

        return $user;
    }
}
