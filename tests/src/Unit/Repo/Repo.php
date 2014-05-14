<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\AbstractRepo;
use BadMethodCallException;
use SplObjectStorage;

class Repo extends AbstractRepo
{
    use Repo1Trait;
    use Repo2Trait;

    private static $instance;

    /**
     * @return User
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new Repo(__NAMESPACE__.'\Model');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this->initializeCalled = true;

        $this->initialize1Trait();
        $this->initialize2Trait();
    }

    public $test;
    public $initializeCalled = false;
    public $initialize1TraitCalled = false;
    public $initialize2TraitCalled = false;

    public function selectWithId($id)
    {
        throw new BadMethodCallException('Test Repo: cannot call selectWithId');
    }

    public function update(SplObjectStorage $models)
    {
        throw new BadMethodCallException('Test Repo: cannot call update');
    }

    public function delete(SplObjectStorage $models)
    {
        throw new BadMethodCallException('Test Repo: cannot call delete');
    }

    public function insert(SplObjectStorage $models)
    {
        throw new BadMethodCallException('Test Repo: cannot call insert');
    }
}
