# Symfony & Doctrine Static Website Generator

This is an example Symfony Flex application which pulls together the following
packages to create a static website generator:

- [doctrine/skeleton-mapper](https://github.com/doctrine/skeleton-mapper)
- [doctrine/static-website-generator](https://github.com/doctrine/static-website-generator)
- [doctrine/doctrine-skeleton-mapper-bundle](https://github.com/doctrine/DoctrineSkeletonMapperBundle)
- [doctrine/doctrine-static-website-generator-bundle](https://github.com/doctrine/DoctrineStaticWebsiteGeneratorBundle)

## Setup

First clone the repository:

    $ git clone git@github.com:jwage/symfony-doctrine-static-website-generator.git

Now run composer install:

    $ cd symfony-doctrine-static-website-generator
    $ composer install

## Building

Now you are ready to build the example static website:

    $ php bin/console doctrine:build-website

## Usage

### Routes

Take a look at the parameter named `doctrine.static_website.routes` in [config/routes.yml](https://github.com/jwage/symfony-doctrine-static-website-generator/blob/master/config/services.yaml)
for defining routes for your static website.

```yaml
parameters:
    doctrine.static_website.routes:
        homepage:
            path: /index.html
            controller: App\Controller\UserController::index

        user:
            path: /user/{username}.html
            controller: App\Controller\UserController::user
            provider: App\Requests\UserRequests::getUsers
```

### Controllers

In your `src/Controller` directory, you can create plain old PHP classes for controllers and map them
using routes.

Once you have created a controller, you have to configure a `ControllerProvider` in the
`config/services.yaml` file and pass it your controllers:

```yaml
services:
    # ...

    Doctrine\StaticWebsiteGenerator\Controller\ControllerProvider:
        arguments:
            -
                - '@App\Controller\UserController'

```

Here is what the `UserController` looks like:

```php
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
```

### Data Sources

Data sources are a simple way to use a PHP service to provide data for a Doctrine Skeleton Mapper model. They
are contained in the `src/DataSources` directory and must implement the `Doctrine\SkeletonMapper\DataSource\DataSource`
interface.

Here is what the `Users` data source looks like:

```php
<?php

declare(strict_types=1);

namespace App\DataSources;

use Doctrine\SkeletonMapper\DataSource\DataSource;

class Users implements DataSource
{
    /**
     * @return mixed[][]
     */
    public function getSourceRows() : array
    {
        return [
            ['username' => 'jwage'],
            ['username' => 'ocramius'],
            ['username' => 'ccovey'],
        ];
    }
}
```

### Models

Models are contained in the `src/Models` directory and are plain PHP classes
that must implement the following interfaces:

- `Doctrine\SkeletonMapper\Hydrator\HydratableInterface`
- `Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface`

Here is what the `User` model looks like:

```php
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
```

### Object Repositories

Object Repositories are contained in the `src/Repositories` directory and must extend the
`Doctrine\SkeletonMapper\ObjectRepository\BasicObjectRepository` class.

Here is what the `UserRepository` looks like:

```php
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
```

### Requests

You can dynamically generate static content by using a route, controller and provider. Take this `user` route
for example:

```yaml
parameters:
    doctrine.static_website.routes:
        # ...

        user:
            path: /user/{username}.html
            controller: App\Controller\UserController::user
            provider: App\Requests\UserRequests::getUsers
```

Note the `App\Requests\UserRequests` class:

```php
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
```

For every user returned by the `getUsers` method, a `/user/{username}.html` file will be generated.
