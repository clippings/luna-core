<?php

namespace Harp\Core\Test\Integration;

use CL\EnvBackup\Env;
use CL\EnvBackup\DirectoryParam;
use Harp\Core\Test\Repo;
use Harp\Core\Test\AbstractTestCase;
use CL\PHPUnitExtensions\ConstrainArrayTrait;

abstract class AbstractIntegrationTestCase extends AbstractTestCase
{
    use ConstrainArrayTrait;

    private $env;

    public function getEnv()
    {
        return $this->env;
    }

    public function setUp()
    {
        parent::setUp();

        if (! is_dir(__DIR__.'/../../repos')) {
            mkdir(__DIR__.'/../../repos');
        }

        $this->env = new Env([
            new DirectoryParam(__DIR__.'/../../repos', [
                'Address.json' => '{
                    "1": {
                        "id": 1,
                        "name": null,
                        "location": "test location"
                    }
                }',
                'Post.json' => '{
                    "1": {
                        "id": 1,
                        "name": "post 1",
                        "body": "my post 1",
                        "userId": 1,
                        "class": "Harp\\\\Core\\\\Test\\\\Model\\\\Post"
                    },
                    "2": {
                        "id": 2,
                        "name": "post 2",
                        "body": "my post 2",
                        "userId": 1,
                        "url": "http:\/\/example.com\/post2",
                        "class": "Harp\\\\Core\\\\Test\\\\Model\\\\BlogPost"
                    }
                }',
                'User.json' => '{
                    "1": {
                        "id": 1,
                        "name": "name",
                        "password": null,
                        "addressId": 1,
                        "deletedAt": null,
                        "createdAt": 1401949982,
                        "updatedAt": 1401949998,
                        "isBlocked": true
                    },
                    "2": {
                        "id": 2,
                        "name": "deleted",
                        "password": null,
                        "addressId": 1,
                        "deletedAt": 1401949982,
                        "createdAt": 1400500528,
                        "updatedAt": 1401949998,
                        "isBlocked": false
                    }
                }',
            ])
        ]);

        $this->env->apply();

        Repo\User::get()->getIdentityMap()->clear();
        Repo\Address::get()->getIdentityMap()->clear();
        Repo\Post::get()->getIdentityMap()->clear();
        Repo\BlogPost::get()->getIdentityMap()->clear();
    }

    public function tearDown()
    {
        $this->env->restore();
    }
}
