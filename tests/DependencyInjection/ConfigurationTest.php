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

namespace Sonata\DashboardBundleBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Sonata\DashboardBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    public function testClasses(): void
    {
        $processor = new Processor();

        $config = $processor->processConfiguration(new Configuration(), [[
            'class' => [
                'dashboard' => 'MyApp\\Sonata\\DashboardBundle\\Entity\\Dashboard',
            ],
            'templates' => [
                'compose' => '@My/MyController/my_template.html.twig',
            ],
        ]]);

        $expected = [
            'class' => [
                'dashboard' => 'MyApp\\Sonata\\DashboardBundle\\Entity\\Dashboard',
                'block' => 'Application\\Sonata\\DashboardBundle\\Entity\\Block',
            ],
            'default_container' => 'sonata.dashboard.block.container',
            'templates' => [
                'compose' => '@My/MyController/my_template.html.twig',
                'compose_container_show' => '@SonataDashboard/DashboardAdmin/compose_container_show.html.twig',
            ],
        ];

        $this->assertEquals($expected, $config);
    }
}
