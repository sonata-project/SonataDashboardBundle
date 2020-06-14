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

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sonata\DashboardBundle\Admin\BlockAdmin;
use Sonata\DashboardBundle\Admin\DashboardAdmin;
use Sonata\DashboardBundle\Block\ContainerBlockService;
use Sonata\DashboardBundle\CmsManager\CmsDashboardManager;
use Sonata\DashboardBundle\CmsManager\CmsManagerSelector;
use Sonata\DashboardBundle\DependencyInjection\SonataDashboardExtension;
use Sonata\DashboardBundle\Entity\BlockInteractor;
use Sonata\DashboardBundle\Entity\BlockManager;
use Sonata\DashboardBundle\Entity\DashboardManager;
use Sonata\DashboardBundle\Listener\RequestListener;
use Sonata\DashboardBundle\Twig\Extension\DashboardExtension;
use Sonata\DashboardBundle\Twig\GlobalVariables;

final class SonataDashboardExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->container->setParameter('kernel.bundles', ['SonataDoctrineBundle' => true]);
    }

    public function testLoadDefault(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService('sonata.dashboard.admin.dashboard', DashboardAdmin::class);
        $this->assertContainerBuilderHasService('sonata.dashboard.admin.block', BlockAdmin::class);
        $this->assertContainerBuilderHasService('sonata.dashboard.block.container', ContainerBlockService::class);
        $this->assertContainerBuilderHasService('sonata.dashboard.cms.dashboard', CmsDashboardManager::class);
        $this->assertContainerBuilderHasService('sonata.dashboard.cms_manager_selector', CmsManagerSelector::class);
        $this->assertContainerBuilderHasService('sonata.dashboard.kernel.request_listener', RequestListener::class);
        $this->assertContainerBuilderHasService('sonata.dashboard.manager.dashboard', DashboardManager::class);
        $this->assertContainerBuilderHasService('sonata.dashboard.manager.block', BlockManager::class);
        $this->assertContainerBuilderHasService('sonata.dashboard.block_interactor', BlockInteractor::class);
        $this->assertContainerBuilderHasService('sonata.dashboard.twig.extension', DashboardExtension::class);
        $this->assertContainerBuilderHasService('sonata.dashboard.twig.global', GlobalVariables::class);

        $this->assertContainerBuilderHasParameter('sonata.dashboard.admin.groupname', 'sonata_dashboard');
        $this->assertContainerBuilderHasParameter('sonata.dashboard.admin.groupicon', "<i class='fa fa-tachometer'></i>");
        $this->assertContainerBuilderHasParameter('sonata.dashboard.admin.dashboard.class', DashboardAdmin::class);
        $this->assertContainerBuilderHasParameter('sonata.dashboard.admin.dashboard.controller', 'SonataDashboardBundle:DashboardAdmin');
        $this->assertContainerBuilderHasParameter('sonata.dashboard.admin.dashboard.translation_domain', 'SonataDashboardBundle');
        $this->assertContainerBuilderHasParameter('sonata.dashboard.admin.block.class', BlockAdmin::class);
        $this->assertContainerBuilderHasParameter('sonata.dashboard.admin.block.controller', 'SonataDashboardBundle:BlockAdmin');
        $this->assertContainerBuilderHasParameter('sonata.dashboard.admin.block.translation_domain', '%sonata.dashboard.admin.dashboard.translation_domain%');
        $this->assertContainerBuilderHasParameter('sonata.dashboard.block.container.class', ContainerBlockService::class);
        $this->assertContainerBuilderHasParameter('sonata.dashboard.manager.dashboard.class', DashboardManager::class);
        $this->assertContainerBuilderHasParameter('sonata.dashboard.manager.block.class', BlockManager::class);
        $this->assertContainerBuilderHasParameter('sonata.dashboard.block_interactor.class', BlockInteractor::class);
        $this->assertContainerBuilderHasParameter('sonata.dashboard.block.class', 'Application\Sonata\DashboardBundle\Entity\Block');
        $this->assertContainerBuilderHasParameter('sonata.dashboard.dashboard.class', 'Application\Sonata\DashboardBundle\Entity\Dashboard');
        $this->assertContainerBuilderHasParameter('sonata.dashboard.admin.block.entity', 'Application\Sonata\DashboardBundle\Entity\Block');
        $this->assertContainerBuilderHasParameter('sonata.dashboard.admin.dashboard.entity', 'Application\Sonata\DashboardBundle\Entity\Dashboard');
        $this->assertContainerBuilderHasParameter('sonata.dashboard.admin.dashboard.templates.compose', '@SonataDashboard/DashboardAdmin/compose.html.twig');
        $this->assertContainerBuilderHasParameter('sonata.dashboard.admin.dashboard.templates.compose_container_show', '@SonataDashboard/DashboardAdmin/compose_container_show.html.twig');
        $this->assertContainerBuilderHasParameter('sonata.dashboard.admin.dashboard.templates.render', '@SonataDashboard/DashboardAdmin/render.html.twig');
        $this->assertContainerBuilderHasParameter('sonata.dashboard.default_container', 'sonata.dashboard.block.container');
    }

    protected function getContainerExtensions(): array
    {
        return [
            new SonataDashboardExtension(),
        ];
    }
}
