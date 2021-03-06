<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\State;
use Harp\Core\Repo\Event;
use Harp\Core\Repo\LinkOne;
use Harp\Core\Repo\LinkMany;
use stdClass;
use Harp\Core\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Core\Model\AbstractModel
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class AbstractModelTest extends AbstractTestCase
{
   /**
     * @covers ::__construct
     * @covers ::getState
     */
    public function testConstrut()
    {
        $model = new Model();

        $this->assertEmpty($model->getUnmapped());
        $this->assertTrue($model->isPending());
        $this->assertEmpty($model->id);
        $this->assertEquals('test', $model->name);

        $model = new Model(['id' => 10, 'name' => 'name1', 'test' => 'testval'], State::SAVED);

        $this->assertEquals(['test' => 'testval'], $model->getUnmapped());
        $this->assertEquals(State::SAVED, $model->getState());
        $this->assertEquals(10, $model->id);
        $this->assertEquals('name1', $model->name);
        $this->assertEquals(['id' => 10, 'name' => 'name1', 'afterConstructCalled' => true], $model->getOriginals());

        $this->assertTrue($model->afterConstructCalled);
    }

    /**
     * @covers ::resetOriginals
     */
    public function testResetOriginals()
    {
        $model = new Model(['id' => 1, 'name' => 'test 2']);

        $expected = ['id' => 1, 'name' => 'test 2', 'afterConstructCalled' => true];

        $model->name = 'test 3';
        $model->id = 4;

        $this->assertEquals($expected, $model->getOriginals());

        $model->resetOriginals();

        $expected = ['id' => 4, 'name' => 'test 3', 'afterConstructCalled' => true];

        $this->assertEquals($expected, $model->getOriginals());
    }

    public function dataSetStateNotVoid()
    {
        return [
            [
                ['id' => null],
                State::VOID,
                State::PENDING,
            ],
            [
                ['id' => 10],
                State::VOID,
                State::SAVED,
            ],
            [
                ['id' => null],
                State::SAVED,
                State::SAVED,
            ],
            [
                ['id' => 10],
                State::PENDING,
                State::PENDING,
            ],
        ];
    }

    /**
     * @dataProvider dataSetStateNotVoid
     * @covers ::setStateNotVoid
     */
    public function testSetStateNotVoid($parameters, $state, $expected)
    {
        $model = new Model($parameters, $state);

        $model->setStateNotVoid();
        $this->assertEquals($expected, $model->getState());
    }

    /**
     * @covers ::getDefaultState
     */
    public function testGetDefaultState()
    {
        $model = new Model();

        $model->setStateNotVoid();
        $this->assertEquals(State::PENDING, $model->getState());
    }


    /**
     * @covers ::setStateVoid
     * @covers ::isVoid
     */
    public function testStateVoid()
    {
        $model = new Model();

        $this->assertFalse($model->isVoid());

        $model->setStateVoid();

        $this->assertEquals(State::VOID, $model->getState());
        $this->assertTrue($model->isVoid());
    }

    /**
     * @covers ::isPending
     * @covers ::setState
     */
    public function testIsPending()
    {
        $model = new Model(null, State::VOID);

        $this->assertFalse($model->isPending());
        $model->setState(State::PENDING);
        $this->assertTrue($model->isPending());
    }


    /**
     * @covers ::isSaved
     * @covers ::setState
     */
    public function testIsSaved()
    {
        $model = new Model(null, State::VOID);

        $this->assertFalse($model->isSaved());
        $model->setState(State::SAVED);
        $this->assertTrue($model->isSaved());
    }

    /**
     * @covers ::isDeleted
     * @covers ::setState
     */
    public function testIsDeleted()
    {
        $model = new Model(null, State::VOID);

        $this->assertFalse($model->isDeleted());
        $model->setState(State::DELETED);
        $this->assertTrue($model->isDeleted());
    }

    /**
     * @covers ::isSoftDeleted
     */
    public function testIsSoftDeleted()
    {
        $model = new Model();

        $this->assertFalse($model->isSoftDeleted());
    }

    /**
     * @covers ::delete
     */
    public function testDelete()
    {
        $model = new Model(null, State::SAVED);

        $this->assertFalse($model->isDeleted());
        $model->delete();
        $this->assertTrue($model->isDeleted());

        $model = new Model(null, State::VOID);

        $this->assertFalse($model->isDeleted());
        $model->delete();
        $this->assertFalse($model->isDeleted(), 'Should not delete if void');
    }

    /**
     * @covers ::delete
     * @expectedException LogicException
     */
    public function testDeletePending()
    {
        $model = new Model(null, State::PENDING);

        $model->delete();
    }

    /**
     * @covers ::getErrors
     * @covers ::isEmptyErrors
     * @covers ::validate
     */
    public function testErrors()
    {
        $model = new Model();

        $this->assertInstanceOf('Harp\Validate\Errors', $model->getErrors());
        $this->assertCount(0, $model->getErrors());
        $this->assertTrue($model->isEmptyErrors());

        $model->name = null;
        $model->other = null;

        $result = $model->validate();
        $this->assertFalse($result);

        $this->assertInstanceOf('Harp\Validate\Errors', $model->getErrors());
        $this->assertCount(2, $model->getErrors());
        $this->assertFalse($model->isEmptyErrors());
        $this->assertCount(1, $model->getErrors()->onlyFor('name'));
        $this->assertCount(1, $model->getErrors()->onlyFor('other'));
    }
}
