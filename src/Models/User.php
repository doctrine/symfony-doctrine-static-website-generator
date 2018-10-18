<?php

declare(strict_types=1);

namespace App\Models;

use Doctrine\SkeletonMapper\Hydrator\HydratableInterface;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;
use Doctrine\SkeletonMapper\ObjectManagerInterface;

class User implements HydratableInterface, LoadMetadataInterface
{
    /** @var string */
    private $username;

    public static function loadMetadata(ClassMetadataInterface $metadata) : void
    {
        $metadata->setIdentifier(['username']);
    }

    /**
     * @param mixed[] $user
     */
    public function hydrate(array $user, ObjectManagerInterface $objectManager) : void
    {
        $this->username = (string) ($user['username'] ?? '');
    }

    public function getUsername() : string
    {
        return $this->username;
    }
}
