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

namespace Sonata\DashboardBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\DashboardBundle\Entity\BlockInteractor;
use Sonata\DashboardBundle\Model\BlockManagerInterface;
use Sonata\DashboardBundle\Tests\Fixtures\Entity\Block;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Vincent Composieux <composieux@ekino.com>
 */
final class BlockInteractorTest extends TestCase
{
    /**
     * Test createNewContainer() method with some values.
     */
    public function testCreateNewContainer(): void
    {
        $blockManager = $this->createMock(BlockManagerInterface::class);
        $blockManager->expects($this->any())->method('create')->will($this->returnValue(new Block()));

        $blockInteractor = new BlockInteractor(
            $this->createMock(RegistryInterface::class),
            $blockManager,
            'sonata.dashboard.block.container'
        );

        $container = $blockInteractor->createNewContainer([
            'enabled' => true,
            'code' => 'my-code',
        ], static function ($container): void {
            $container->setSetting(
                'layout',
                '<div class="custom-layout">{{ CONTENT }}</div>'
            );
        });

        $this->assertInstanceOf(BlockInterface::class, $container);

        $settings = $container->getSettings();

        $this->assertTrue($container->getEnabled());

        $this->assertSame('my-code', $settings['code']);
        $this->assertSame(
            '<div class="custom-layout">{{ CONTENT }}</div>',
            $settings['layout']
        );
    }
}
