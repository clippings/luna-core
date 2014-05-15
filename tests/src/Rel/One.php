<?php

namespace CL\LunaCore\Test\Rel;

use CL\LunaCore\Test\Repo\AbstractTestRepo;
use CL\LunaCore\Rel\UpdateInterface;
use CL\LunaCore\Rel\AbstractRelOne;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Repo\AbstractLink;
use CL\Util\Arr;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class One extends AbstractRelOne implements UpdateInterface
{
    private $key;

    public function __construct($name, AbstractTestRepo $repo, AbstractTestRepo $foreignRepo, array $options = array())
    {
        $this->key = lcfirst($foreignRepo->getName()).'Id';

        parent::__construct($name, $repo, $foreignRepo, $options);
    }

    public function areLinked(AbstractModel $model, AbstractModel $foreign)
    {
        return $model->{$this->key} == $foreign->getId();
    }

    public function hasForeign(array $models)
    {
        $keys = Arr::pluckUniqueProperty($models, $this->key);

        return ! empty($keys);
    }

    public function loadForeign(array $models)
    {
        return $this->getForeignRepo()
            ->findByKey('id', Arr::pluckUniqueProperty($models, $this->key));
    }

    public function update(AbstractModel $model, AbstractLink $link)
    {
        $model->{$this->key} = $link->get()->getId();
    }
}
