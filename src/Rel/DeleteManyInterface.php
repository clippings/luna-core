<?php

namespace CL\LunaCore\Rel;

use CL\LunaCore\Repo\LinkMany;
use CL\LunaCore\Model\AbstractModel;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface DeleteManyInterface
{
    public function delete(AbstractModel $model, LinkMany $link);
}
