<?php

namespace Harp\Core\Test\Unit\Repo;

/**
 * @coversDefaultClass Harp\Core\Repo\AbstractLink
 */
class AbstractRelTest extends AbstractRepoTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRel
     */
    public function testConstruct()
    {
        $repo1 = new Repo(__NAMESPACE__.'\Model');
        $repo2 = new Repo(__NAMESPACE__.'\Model');
        $rel = new RelOne('test', $repo1, $repo2);

        $link = $this->getMockForAbstractClass('Harp\Core\Repo\AbstractLink', [$rel]);
        $this->assertSame($rel, $link->getRel());
    }
}
