<?php

namespace CL\LunaCore\Test\Unit\Repo;

use CL\LunaCore\Repo\AbstractRepo;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Repo\ModelEvent;
use CL\LunaCore\Util\Objects;
use CL\Carpo\Assert\Present;

class AbstractRepoTest extends AbstractRepoTestCase
{
    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::__construct
     * @covers CL\LunaCore\Repo\AbstractRepo::getModelClass
     */
    public function testConstruct()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $this->assertEquals(__NAMESPACE__.'\Model', $repo->getModelClass());
    }

    public function getRepoInitialized($initialized)
    {
        $repo = $this->getMockForAbstractClass(
            'CL\LunaCore\Repo\AbstractRepo',
            [__NAMESPACE__.'\Model']
        );

        $repo
            ->expects($initialized ? $this->once() : $this->never())
            ->method('initialize');

        return $repo;
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::getName
     */
    public function testGetName()
    {
        $repo = $this->getRepoInitialized(false);

        $this->assertEquals('Model', $repo->getName());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::getPrimaryKey
     * @covers CL\LunaCore\Repo\AbstractRepo::setPrimaryKey
     */
    public function testPrimaryKey()
    {
        $repo = $this->getRepoInitialized(true);

        $this->assertEquals('id', $repo->getPrimaryKey());

        $repo->setPrimaryKey('guid');

        $this->assertEquals('guid', $repo->getPrimaryKey());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::getLinkMap
     */
    public function testGetLinkMap()
    {
        $repo = $this->getRepoInitialized(false);

        $this->assertInstanceof('CL\LunaCore\Repo\LinkMap', $repo->getLinkMap());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::getIdentityMap
     */
    public function testGetIdentityMap()
    {
        $repo = $this->getRepoInitialized(false);

        $this->assertInstanceof('CL\LunaCore\Repo\IdentityMap', $repo->getIdentityMap());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::getModelReflection
     */
    public function testGetModelReflection()
    {
        $repo = $this->getRepoInitialized(false);

        $this->assertInstanceof('ReflectionClass', $repo->getModelReflection());
        $this->assertEquals(__NAMESPACE__.'\Model', $repo->getModelReflection()->getName());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::getRels
     * @covers CL\LunaCore\Repo\AbstractRepo::setRels
     */
    public function testRels()
    {
        $repo = $this->getRepoInitialized(true);

        $this->assertInstanceof('CL\LunaCore\Repo\Rels', $repo->getRels());

        $rels = [
            $this->getRelOne(),
            $this->getRelMany()
        ];

        $expected = [
            'one' => $rels[0],
            'many' => $rels[1],
        ];

        $repo->setRels($rels);

        $this->assertSame($expected, $repo->getRels()->all());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::getEventListeners
     * @covers CL\LunaCore\Repo\AbstractRepo::addEventBeforeDelete
     * @covers CL\LunaCore\Repo\AbstractRepo::addEventBeforeSave
     * @covers CL\LunaCore\Repo\AbstractRepo::addEventBeforeInsert
     * @covers CL\LunaCore\Repo\AbstractRepo::addEventBeforeUpdate
     * @covers CL\LunaCore\Repo\AbstractRepo::addEventAfterDelete
     * @covers CL\LunaCore\Repo\AbstractRepo::addEventAfterSave
     * @covers CL\LunaCore\Repo\AbstractRepo::addEventAfterInsert
     * @covers CL\LunaCore\Repo\AbstractRepo::addEventAfterUpdate
     * @covers CL\LunaCore\Repo\AbstractRepo::addEventAfterLoad
     */
    public function testEventListeners()
    {
        $repo = $this->getRepoInitialized(true);

        $this->assertInstanceof('CL\LunaCore\Repo\EventListeners', $repo->getEventListeners());

        $repo
            ->addEventBeforeDelete('before delete callback')
            ->addEventBeforeSave('before save callback')
            ->addEventBeforeInsert('before insert callback')
            ->addEventBeforeUpdate('before update callback')
            ->addEventAfterDelete('after delete callback')
            ->addEventAfterSave('after save callback')
            ->addEventAfterInsert('after insert callback')
            ->addEventAfterUpdate('after update callback')
            ->addEventAfterLoad('after load callback');

        $expectedBefore = [
          ModelEvent::DELETE => ['before delete callback'],
          ModelEvent::SAVE   => ['before save callback'],
          ModelEvent::INSERT => ['before insert callback'],
          ModelEvent::UPDATE => ['before update callback'],
        ];

        $expectedAfter = [
          ModelEvent::DELETE => ['after delete callback'],
          ModelEvent::SAVE   => ['after save callback'],
          ModelEvent::INSERT => ['after insert callback'],
          ModelEvent::UPDATE => ['after update callback'],
          ModelEvent::LOAD   => ['after load callback'],
        ];

        $this->assertEquals($expectedBefore, $repo->getEventListeners()->getBefore());
        $this->assertEquals($expectedAfter, $repo->getEventListeners()->getAfter());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::getAsserts
     * @covers CL\LunaCore\Repo\AbstractRepo::setAsserts
     */
    public function testAsserts()
    {
        $repo = $this->getRepoInitialized(true);

        $this->assertInstanceof('CL\Carpo\Asserts', $repo->getAsserts());

        $asserts = [
            new Present('name'),
        ];

        $repo->setAsserts($asserts);

        $this->assertSame($asserts, Objects::toArray($repo->getAsserts()->all()));
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::hasBeforeEvent
     * @covers CL\LunaCore\Repo\AbstractRepo::hasAfterEvent
     * @covers CL\LunaCore\Repo\AbstractRepo::dispatchBeforeEvent
     * @covers CL\LunaCore\Repo\AbstractRepo::dispatchAfterEvent
     */
    public function testDispatchEvents()
    {
        $models = [new Model()];

        $eventListener = $this->getMock('CL\LunaCore\Repo\EventListeners');

        $repo = $this->getMockForAbstractClass(
            'CL\LunaCore\Repo\AbstractRepo',
            [__NAMESPACE__.'\Model'],
            '',
            true,
            true,
            true,
            ['getEventListeners']
        );

        $repo
            ->expects($this->exactly(4))
            ->method('getEventListeners')
            ->will($this->returnValue($eventListener));

        $eventListener
            ->expects($this->once())
            ->method('dispatchBeforeEvent')
            ->with($this->equalTo(ModelEvent::LOAD), $this->identicalTo($models));

        $repo->dispatchBeforeEvent(ModelEvent::LOAD, $models);


        $eventListener
            ->expects($this->once())
            ->method('dispatchAfterEvent')
            ->with($this->equalTo(ModelEvent::LOAD), $this->identicalTo($models));

        $repo->dispatchAfterEvent(ModelEvent::LOAD, $models);


        $eventListener
            ->expects($this->once())
            ->method('hasBeforeEvent')
            ->with($this->equalTo(ModelEvent::LOAD));

        $repo->hasBeforeEvent(ModelEvent::LOAD);


        $eventListener
            ->expects($this->once())
            ->method('hasAfterEvent')
            ->with($this->equalTo(ModelEvent::LOAD));

        $repo->hasAfterEvent(ModelEvent::LOAD);
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::newInstance
     */
    public function testNewInstance()
    {
        $repo = $this->getRepoInitialized(false);

        $model = $repo->newInstance();

        $this->assertInstanceOf(__NAMESPACE__.'\Model', $model);
        $this->assertEquals(['id' => null, 'name' => 'test'], $model->getProperties());
        $this->assertTrue($model->isPending());

        $model = $repo->newInstance(['id' => 10, 'name' => 'new'], AbstractModel::PERSISTED);

        $this->assertEquals(['id' => 10, 'name' => 'new'], $model->getProperties());
        $this->assertTrue($model->isPersisted());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::newVoidInstance
     */
    public function testNewVoidInstance()
    {
        $repo = $this->getRepoInitialized(false);

        $model = $repo->newVoidInstance();

        $this->assertInstanceOf(__NAMESPACE__.'\Model', $model);
        $this->assertEquals(['id' => null, 'name' => 'test'], $model->getProperties());
        $this->assertTrue($model->isVoid());

        $model = $repo->newVoidInstance(['id' => 10, 'name' => 'new']);

        $this->assertEquals(['id' => 10, 'name' => 'new'], $model->getProperties());
        $this->assertTrue($model->isVoid());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::getInitialized
     * @covers CL\LunaCore\Repo\AbstractRepo::initializeOnce
     */
    public function testGetInitialized()
    {
        $repo = $this->getRepoInitialized(true);

        $this->assertFalse($repo->getInitialized());

        $repo->initializeOnce();

        $this->assertTrue($repo->getInitialized());

        $repo->initializeOnce();

        $this->assertTrue($repo->getInitialized(), 'Should remaind initialized, but initializeAll Should be called only once');
    }

    public function testInitialize()
    {
        $repo = new Repo(__NAMESPACE__.'\Model');

        $this->assertFalse($repo->initializeCalled);
        $this->assertFalse($repo->initialize1TraitCalled);
        $this->assertFalse($repo->initialize2TraitCalled);

        $repo->initialize();

        $this->assertTrue($repo->initializeCalled);
        $this->assertTrue($repo->initialize1TraitCalled);
        $this->assertTrue($repo->initialize2TraitCalled);
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::find
     */
    public function testFind()
    {
        $repo = $this->getMockForAbstractClass(
            'CL\LunaCore\Repo\AbstractRepo',
            [__NAMESPACE__.'\Model'],
            '',
            true,
            true,
            true,
            ['selectWithId']
        );

        $model = new Model();

        $repo
            ->expects($this->exactly(2))
            ->method('selectWithId')
            ->will($this->returnValueMap([
                [2, $model],
                [5, null],
            ]));

        $result = $repo->find(2);

        $this->assertSame($model, $result);

        $result = $repo->find(5);

        $this->assertInstanceOf(__NAMESPACE__.'\Model', $result);
        $this->assertEquals(['id' => null, 'name' => 'test'], $result->getProperties());
        $this->assertTrue($result->isVoid());
    }

    /**
     * @covers CL\LunaCore\Repo\AbstractRepo::persist
     */
    public function testPersist()
    {
        $repo = $this->getMockForAbstractClass(
            'CL\LunaCore\Repo\AbstractRepo',
            [__NAMESPACE__.'\Model'],
            '',
            true,
            true,
            true,
            ['validateModel', 'newPersist']
        );

        $model = new Model();

        $persist = $this->getMock('CL\LunaCore\Repo\Persist', ['add', 'execute']);
        $persist
            ->expects($this->once())
            ->method('add')
            ->with($this->identicalTo($model))
            ->will($this->returnSelf());

        $persist
            ->expects($this->once())
            ->method('execute');

        $repo
            ->expects($this->once())
            ->method('newPersist')
            ->will($this->returnValue($persist));

        $repo
            ->expects($this->once())
            ->method('validateModel')
            ->with($this->identicalTo($model));

        $repo->persist($model);
    }
}
