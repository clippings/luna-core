<?php

namespace Harp\Core\Test\Unit\Model;

use Harp\Core\Repo\AbstractRepo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class RepoMock extends AbstractRepo
{
    public function initialize()
    {
        $this
            ->setModelClass(__NAMESPACE__.'\ModelMock');
    }
}