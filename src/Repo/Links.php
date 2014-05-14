<?php

namespace CL\LunaCore\Repo;

use SplObjectStorage;
use Closure;

use CL\LunaCore\Model\AbstractModel;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Links
{
    /**
     * @var AbstractModel
     */
    private $model;

    /**
     * @var AbstractLink[]
     */
    private $items = [];

    /**
     * @param AbstractModel $model
     */
    function __construct(AbstractModel $model)
    {
        $this->model = $model;
    }

    /**
     * @return AbstractModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return AbstractLink[]
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * @param string       $name
     * @param AbstractLink $link
     * @return Links $this
     */
    public function add($name, AbstractLink $link)
    {
        $this->items[$name] = $link;

        return $this;
    }

    /**
     * Get all of the linked models
     *
     * @return SplObjectStorage
     */
    public function getModels()
    {
        $all = new SplObjectStorage();

        foreach ($this->items as $item) {
            $all->addAll($item->getCurrentAndOriginal());
        }

        return $all;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * @param  string  $name
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->items[$name]);
    }

    /**
     * @param  string $name
     * @return AbstractLink|null
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->items[$name];
        }
    }
}
