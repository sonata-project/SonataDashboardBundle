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

namespace Sonata\DashboardBundle\Tests\Dashboard;

use Sonata\DashboardBundle\CmsManager\CmsDashboardManager;
use Sonata\DashboardBundle\Tests\Fixtures\Entity\CmsBlock;
use Sonata\DashboardBundle\Tests\Model\Dashboard;

final class CmsDashboardManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CmsDashboardManager
     */
    private $manager;

    /**
     * Setup manager object to test.
     */
    public function setUp(): void
    {
        $this->blockInteractor = $this->getMockBlockInteractor();
        $this->dashboardManager = $this->createMock('Sonata\DashboardBundle\Model\DashboardManagerInterface');
        $this->manager = new CmsDashboardManager($this->dashboardManager, $this->blockInteractor);
    }

    /**
     * Test finding an existing container in a dashboard.
     */
    public function testFindExistingContainer(): void
    {
        $block = new CmsBlock();
        $block->setSettings(['code' => 'findme']);

        $dashboard = new Dashboard();
        $dashboard->addBlocks($block);

        $container = $this->manager->findContainer('findme', $dashboard);

        $this->assertSame(spl_object_hash($block), spl_object_hash($container),
            'should retrieve the block of the dashboard');
    }

    /**
     * Test finding an non-existing container in a dashboard does create a new block.
     */
    public function testFindNonExistingContainerCreatesNewBlock(): void
    {
        $dashboard = new Dashboard();

        $container = $this->manager->findContainer('newcontainer', $dashboard);

        $this->assertInstanceOf('Sonata\DashboardBundle\Model\DashboardBlockInterface', $container, 'should be a block');
        $this->assertSame('newcontainer', $container->getSetting('code'));
    }

    /**
     * Test get Dashboard method with id return Dashboard.
     */
    public function testGetDashboardWithId(): void
    {
        $dashboardManager = $this->createMock('Sonata\DashboardBundle\Model\DashboardManagerInterface');

        $dashboardManager->expects($this->any())->method('findOneBy')->will($this->returnValue(new Dashboard()));
        $this->blockInteractor->expects($this->any())->method('loadDashboardBlocks')->will($this->returnValue([]));

        $manager = $this->createManager($dashboardManager, $this->blockInteractor);

        $dashboard = 1;

        $this->assertInstanceOf('Sonata\DashboardBundle\Model\DashboardInterface', $manager->getDashboard($dashboard));
    }

    /**
     * Test get Dashboard method with id throw Exception.
     */
    public function testGetDashboardWithIdException(): void
    {
        $dashboardManager = $this->createMock('Sonata\DashboardBundle\Model\DashboardManagerInterface');

        $this->blockInteractor->expects($this->any())->method('loadDashboardBlocks')->will($this->returnValue([]));

        $manager = $this->createManager($dashboardManager, $this->blockInteractor);

        $dashboard = 1;

        $dashboardManager->expects($this->any())->method('findOneBy')->will($this->returnValue(null));
        $manager = $this->createManager($dashboardManager, $this->blockInteractor);

        $this->expectException('\Sonata\DashboardBundle\Exception\DashboardNotFoundException');
        $manager->getDashboard($dashboard);
    }

    /**
     * Test get Dashboard method without param return Dashboard.
     */
    public function testGetDashboardWithoutParam(): void
    {
        $dashboardManager = $this->createMock('Sonata\DashboardBundle\Model\DashboardManagerInterface');

        $dashboardManager->expects($this->any())->method('findOneBy')->will($this->returnValue(new Dashboard()));
        $this->blockInteractor->expects($this->any())->method('loadDashboardBlocks')->will($this->returnValue([]));

        $manager = $this->createManager($dashboardManager, $this->blockInteractor);
        $manager->setCurrentDashboard(new Dashboard());
        $dashboard = null;

        $this->assertInstanceOf('Sonata\DashboardBundle\Model\DashboardInterface', $manager->getDashboard($dashboard));
    }

    /**
     * Test get Dashboard method without param throw Exception.
     */
    public function testGetDashboardWithoutParamException(): void
    {
        $dashboardManager = $this->createMock('Sonata\DashboardBundle\Model\DashboardManagerInterface');

        $this->blockInteractor->expects($this->any())->method('loadDashboardBlocks')->will($this->returnValue([]));

        $manager = $this->createManager($dashboardManager, $this->blockInteractor);

        $dashboard = null;

        $dashboardManager->expects($this->any())->method('findOneBy')->will($this->returnValue(null));
        $manager = $this->createManager($dashboardManager, $this->blockInteractor);

        $this->expectException('\Sonata\DashboardBundle\Exception\DashboardNotFoundException');
        $manager->getDashboard($dashboard);
    }

    /**
     * Returns a mock block interactor.
     *
     * @return BlockInteractorInterface
     */
    private function getMockBlockInteractor()
    {
        $callback = function ($options) {
            $block = new CmsBlock();
            $block->setSettings($options);

            return $block;
        };

        $mock = $this->createMock('Sonata\DashboardBundle\Model\BlockInteractorInterface');
        $mock->expects($this->any())->method('createNewContainer')->will($this->returnCallback($callback));

        return $mock;
    }

    /**
     * Returns a cms dashboard manager.
     *
     * @return CmsDashboardManager
     */
    private function createManager($dashboardManager, $blockInteractor)
    {
        return new CmsDashboardManager($dashboardManager, $blockInteractor);
    }
}
