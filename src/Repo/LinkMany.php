<?php

namespace CL\LunaCore\Repo;

use Countable;
use Closure;
use Iterator;
use SplObjectStorage;

use CL\LunaCore\Rel\AbstractRel;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Util\Objects;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinkMany extends AbstractLink implements Countable, Iterator
{
    protected $current;
    protected $original;

    public function __construct(AbstractRel $rel, array $current)
    {
        parent::__construct($rel);

        $this->current = new SplObjectStorage();

        $this->set($current);

        $this->original = clone $this->current;
    }

    public function set(array $current)
    {
        foreach ($current as $item) {
            $this->add($item);
        }

        return $this;
    }

    public function clear()
    {
        $this->current = new SplObjectStorage();

        return $this;
    }

    public function add(AbstractModel $model)
    {
        $this->current->attach($model);

        return $this;
    }

    public function remove(AbstractModel $model)
    {
        $this->current->remove($model);

        return $this;
    }

    public function isEmpty()
    {
        return count($this->current) === 0;
    }

    public function has(AbstractModel $model)
    {
        return $this->current->contains($model);
    }

    public function hasId($id)
    {
        return array_search($id, $this->getIds()) !== false;
    }

    public function all()
    {
        return $this->current;
    }

    public function getOriginal()
    {
        return $this->original;
    }

    public function getOriginalIds()
    {
        return Objects::invoke($this->original, 'getId');
    }

    public function getIds()
    {
        return Objects::invoke($this->current, 'getId');
    }

    public function getAdded()
    {
        $added = clone $this->current;
        $added->removeAll($this->original);

        return $added;
    }

    public function getAddedIds()
    {
        return Objects::invoke($this->getAdded(), 'getId');
    }

    public function getRemoved()
    {
        $removed = clone $this->original;
        $removed->removeAll($this->current);

        return $removed;
    }

    public function getRemovedIds()
    {
        return Objects::invoke($this->getRemoved(), 'getId');
    }

    public function getAll()
    {
        $all = clone $this->current;
        $all->addAll($this->original);

        return $all;
    }

    public function count()
    {
        return $this->current->count();
    }

    public function current()
    {
        return $this->current->current();
    }

    public function key()
    {
        return $this->current->key();
    }

    public function next()
    {
        return $this->current->next();
    }

    public function rewind()
    {
        $this->current->rewind();

        return $this;
    }

    public function valid()
    {
        return $this->current->valid();
    }

    public function getFirst()
    {
        $this->current->rewind();

        if ($this->current->valid()) {
            return $this->current->current();
        } else {
            return $this->getRel()->getForeignRepo()->newVoidInstance();
        }
    }
}
