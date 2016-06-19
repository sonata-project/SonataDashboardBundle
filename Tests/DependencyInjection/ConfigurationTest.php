<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DashboardBundleBundle\Tests\DependencyInjection;

use Sonata\DashboardBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testClasses()
    {
        $processor = new Processor();

        $config = $processor->processConfiguration(new Configuration(), array(array(
            'class' => array(
                'dashboard' => 'MyApp\\Sonata\\DashboardBundle\\Entity\\Dashboard',
            ),
            'templates' => array(
                'compose' => 'MyBundle:MyController:my_template.html.twig',
            ),
        )));

        $expected = array(
            'class' => array(
                'dashboard' => 'MyApp\\Sonata\\DashboardBundle\\Entity\\Dashboard',
                'block' => 'Application\\Sonata\\DashboardBundle\\Entity\\Block',
            ),
            'default_container' => 'sonata.dashboard.block.container',
            'templates' => array(
                'compose' => 'MyBundle:MyController:my_template.html.twig',
                'compose_container_show' => 'SonataDashboardBundle:DashboardAdmin:compose_container_show.html.twig',                            
            ),
        );

        $this->assertEquals($expected, $config);
    }
}
