<?php

namespace Harp\Core\Test\Rel;

use Harp\Core\Test\Repo\TestRepo;
use Harp\Core\Rel\AbstractRelMany;
use Harp\Core\Rel\UpdateManyInterface;
use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\Models;
use Harp\Core\Repo\LinkMany;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Many extends AbstractRelMany implements UpdateManyInterface
{
    private $key;

    public function __construct($name, TestRepo $repo, TestRepo $foreignRepo, array $options = array())
    {
        $this->key = lcfirst($repo->getName()).'Id';

        parent::__construct($name, $repo, $foreignRepo, $options);
    }

    public function areLinked(AbstractModel $model, AbstractModel $foreign)
    {
        return $model->getId() == $foreign->{$this->key};
    }

    public function hasForeign(Models $models)
    {
        return ! $models->isEmptyProperty($this->getRepo()->getPrimaryKey());
    }

    public function loadForeign(Models $models, $flags = null)
    {
        $keys = $models->pluckPropertyUnique($this->getRepo()->getPrimaryKey());

        return $this->getForeignRepo()
            ->findAll()
            ->whereIn($this->key, $keys)
            ->setFlags($flags)
            ->loadRaw();
    }

    public function update(LinkMany $link)
    {
        foreach ($link->getAdded() as $added) {
            $added->{$this->key} = $link->getModel()->getId();
        }

        foreach ($link->getRemoved() as $added) {
            $added->{$this->key} = null;
        }
    }
}
