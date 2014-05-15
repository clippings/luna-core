<?php

namespace CL\LunaCore\Repo;

use SplObjectStorage;

use CL\LunaCore\Model\AbstractModel;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class EventListeners {

    /**
     * @param  array       $listeners
     * @param  int        $event
     * @param  AbstractModel $target
     */
    public static function dispatchEvent($listeners, $event, AbstractModel $target)
    {
        if (isset($listeners[$event])) {
            foreach ($listeners[$event] as $listner) {
                call_user_func($listner, $target);
            }
        }
    }

    /**
     * @var array
     */
    protected $before = [];

    /**
     * @var array
     */
    protected $after = [];

    /**
     * @return array
     */
    public function getBefore()
    {
        return $this->before;
    }

    /**
     * @return array
     */
    public function getAfter()
    {
        return $this->after;
    }

    /**
     * @param int $event
     * @param string|array|Closure $listener
     */
    public function addBefore($event, $listener)
    {
        $this->before[$event] []= $listener;

        return $this;
    }

    /**
     * @param int $event
     * @param string|array|Closure $listener
     */
    public function addAfter($event, $listener)
    {
        $this->after[$event] []= $listener;

        return $this;
    }

    /**
     * @param  int  $event
     * @return boolean
     */
    public function hasBeforeEvent($event)
    {
        return isset($this->before[$event]);
    }

    /**
     * @param  int  $event
     * @return boolean
     */
    public function hasAfterEvent($event)
    {
        return isset($this->after[$event]);
    }

    /**
     * @param  AbstractModel[]|SplObjectStorage  $models
     * @param  int  $event
     */
    public function dispatchAfterEvent($models, $event)
    {
        foreach ($models as $model) {
            self::dispatchEvent($this->after, $event, $model);
        }
    }

    /**
     * @param  AbstractModel[]|SplObjectStorage  $models
     * @param  int  $event
     */
    public function dispatchBeforeEvent($models, $event)
    {
        foreach ($models as $model) {
            self::dispatchEvent($this->before, $event, $model);
        }
    }
}
