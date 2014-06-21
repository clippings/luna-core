<?php

namespace Harp\Core\Repo;

use Harp\Core\Model\AbstractModel;

/**
 * Each loaded model is passed through the IdentityMap. If another model with the same ID is already present,
 * then that model is returned. This means that you will retrieve the same object each time you load models
 * with the same ID.
 *
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class IdentityMap
{
    /**
     * @var AbstractModel[]
     */
    private $models = [];

    /**
     * @var AbstractRepo
     */
    private $repo;

    public function __construct(AbstractRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @var AbstractRepo
     */
    public function getRepo()
    {
        return $this->repo;
    }

    /**
     * @var AbstractModel[]
     */
    public function getModels()
    {
        return $this->models;
    }

    /**
     * If a model with the same key already exist in the identity map return that model.
     * Only handle 'saved' models.
     *
     * @param  AbstractModel $model
     * @return AbstractModel
     */
    public function get(AbstractModel $model)
    {
        if ($model->isSaved()) {
            $key = $model->getId();

            if (isset($this->models[$key])) {
                $model = $this->models[$key];
            } else {
                $this->models[$key] = $model;
            }
        }

        return $model;
    }

    /**
     * @param  AbstractModel $model
     * @return boolean
     */
    public function has(AbstractModel $model)
    {
        return isset($this->models[$model->getId()]);
    }

    /**
     * Call the "get" method for a whole array of models
     *
     * @param  AbstractModel[] $models
     * @return AbstractModel[]
     */
    public function getArray(array $models)
    {
        return array_map(function ($model) {
            return $this->get($model);
        }, $models);
    }

    /**
     * @return IdentityMap $this
     */
    public function clear()
    {
        $this->models = [];

        return $this;
    }
}
