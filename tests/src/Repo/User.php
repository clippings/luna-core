<?php

namespace Harp\Core\Test\Repo;

use Harp\Core\Test\Rel;
use Harp\Core\Test\Model;
use Harp\Validate\Assert;
use Harp\Serializer;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class User extends AbstractTestRepo {

    public function initialize()
    {
        $this
            ->setModelClass('Harp\Core\Test\Model\User')
            ->setFile('User.json')
            ->addRels([
                new Rel\One('address', $this, Address::get()),
                (new Rel\Many('posts', $this, Post::get()))
                    ->setLinkClass(__NAMESPACE__.'\LinkManyPosts'),
            ])
            ->setSoftDelete(true)
            ->addAsserts([
                new Assert\Present('name'),
            ])
            ->addSerializers([
                new Serializer\Json('profile'),
            ]);

    }
}
