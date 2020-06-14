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

namespace Sonata\DashboardBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Sonata\DashboardBundle\DependencyInjection\Configuration;
use Sonata\DashboardBundle\DependencyInjection\SonataDashboardExtension;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

final class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    public function testDefault(): void
    {
        $this->assertProcessedConfigurationEquals([
            'class' => [
                'dashboard' => 'Application\Sonata\DashboardBundle\Entity\Dashboard',
                'block' => 'Application\Sonata\DashboardBundle\Entity\Block',
            ],
            'templates' => [
                'compose' => '@SonataDashboard/DashboardAdmin/compose.html.twig',
                'compose_container_show' => '@SonataDashboard/DashboardAdmin/compose_container_show.html.twig',
                'render' => '@SonataDashboard/DashboardAdmin/render.html.twig',
            ],
            'default_container' => 'sonata.dashboard.block.container',
        ], [
            __DIR__.'/../Fixtures/configuration.yaml',
        ]);
    }

    protected function getContainerExtension(): ExtensionInterface
    {
        return new SonataDashboardExtension();
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
