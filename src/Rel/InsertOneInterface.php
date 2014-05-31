<?php

namespace Harp\Core\Rel;

use Harp\Core\Repo\LinkOne;
use Harp\Core\Model\AbstractModel;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface InsertOneInterface
{
    public function insert(AbstractModel $model, LinkOne $link);
}