<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DashboardBundle\Tests\Twig;

use Sonata\DashboardBundle\Twig\GlobalVariables;

/**
 * GlobalVariables.
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
final class GlobalVariablesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CmsManagerSelector
     */
    private $cmsManagerSelector;

    /**
     * @var GlobalVariables
     */
    private $globals;

    public function setUp()
    {
        $this->cmsManagerSelector = $this->getMockCmsManagerSelector();
        $this->globals = new GlobalVariables($this->cmsManagerSelector);
    }

    /**
     * @return CmsManagerInterface
     */
    public function testGetCmsManager()
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
        $cms = $this->getMock('Sonata\DashboardBundle\CmsManager\CmsManagerSelectorInterface');

        $mock = $this->getMock('Sonata\DashboardBundle\CmsManager\CmsManagerInterface');
        $cms->expects($this->any())->method('retrieve')->willReturn($mock);
        $cms->expects($this->any())->method('isEditor')->willReturn(true);

        return $cms;
    }
}
