<?php

/*
 * This file is part of the Sonata package.
*
* (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sonata\DashboardBundle\Tests\Twig;

use Sonata\DashboardBundle\Twig\GlobalVariables;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * GlobalVariables.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class GlobalVariablesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var GlobalVariables
     */
    private $globals;

    public function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->container->setParameter('sonata.dashboard.is_inline_edition_on', false);
        $this->globals = new GlobalVariables($this->container);
    }

    /**
     * @return bool
     */
    public function testIsInlineEditionOn()
    {
        $this->assertFalse($this->globals->isInlineEditionOn());
    }
}
