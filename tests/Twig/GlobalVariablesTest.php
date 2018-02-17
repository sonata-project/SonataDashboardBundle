<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DashboardBundle\Tests\Twig;

use Sonata\DashboardBundle\CmsManager\CmsManagerSelectorInterface;
use Sonata\DashboardBundle\Twig\GlobalVariables;

/**
 * GlobalVariables.
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
final class GlobalVariablesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CmsManagerSelectorInterface
     */
    private $cmsManagerSelector;

    /**
     * @var GlobalVariables
     */
    private $globals;

    public function setUp(): void
    {
        $this->cmsManagerSelector = $this->getMockCmsManagerSelector();
        $this->globals = new GlobalVariables($this->cmsManagerSelector);
    }

    public function testGetCmsManager(): void
    {
        $this->assertInstanceOf('Sonata\DashboardBundle\CmsManager\CmsManagerInterface', $this->globals->getCmsManager());
    }

    /**
     * @return bool
     */
    public function testIsEditor()
    {
        $this->assertTrue($this->globals->isEditor());
    }

    /**
     * Returns a cms manager selector.
     *
     * @return CmsManagerSelectorInterface
     */
    private function getMockCmsManagerSelector()
    {
        $cms = $this->createMock('Sonata\DashboardBundle\CmsManager\CmsManagerSelectorInterface');

        $mock = $this->createMock('Sonata\DashboardBundle\CmsManager\CmsManagerInterface');
        $cms->expects($this->any())->method('retrieve')->willReturn($mock);
        $cms->expects($this->any())->method('isEditor')->willReturn(true);

        return $cms;
    }
}
