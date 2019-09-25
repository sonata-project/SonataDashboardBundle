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

namespace Sonata\DashboardBundle\Tests\Block;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContext;
use Sonata\BlockBundle\Model\Block;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Test\BlockServiceTestCase;
use Sonata\DashboardBundle\Block\ContainerBlockService;

final class ContainerBlockServiceTest extends BlockServiceTestCase
{
    /**
     * test the block execute() method.
     */
    public function testExecute(): void
    {
        $service = new ContainerBlockService('core.container', $this->templating);

        $block = new Block();
        $block->setName('block.name');
        $block->setType('core.container');
        $block->setSettings([
            'code' => 'block.code',
        ]);

        $blockContext = new BlockContext($block, [
            'code' => '',
            'layout' => '{{ CONTENT }}',
            'class' => '',
            'template' => '@SonataDashboard/BlockAdmin/block_container.html.twig',
        ]);

        $service->execute($blockContext);

        $this->assertSame('@SonataDashboard/BlockAdmin/block_container.html.twig', $this->templating->view);
        $this->assertSame('block.code', $this->templating->parameters['block']->getSetting('code'));
        $this->assertSame('block.name', $this->templating->parameters['block']->getName());
        $this->assertInstanceOf(BlockInterface::class, $this->templating->parameters['block']);
    }

    /**
     * test the container layout.
     */
    public function testLayout(): void
    {
        $service = new ContainerBlockService('core.container', $this->templating);

        $block = new Block();
        $block->setName('block.name');
        $block->setType('core.container');

        // we manually perform the settings merge
        $blockContext = new BlockContext($block, [
            'code' => 'block.code',
            'layout' => 'before{{ CONTENT }}after',
            'class' => '',
            'template' => '@SonataDashboard/BlockAdmin/block_container.html.twig',
        ]);

        $service->execute($blockContext);

        $this->assertInternalType('array', $this->templating->parameters['decorator']);
        $this->assertArrayHasKey('pre', $this->templating->parameters['decorator']);
        $this->assertArrayHasKey('post', $this->templating->parameters['decorator']);
        $this->assertSame('before', $this->templating->parameters['decorator']['pre']);
        $this->assertSame('after', $this->templating->parameters['decorator']['post']);
    }

    /**
     * test the block's form builders.
     */
    public function testFormBuilder(): void
    {
        $service = new ContainerBlockService('core.container', $this->templating);

        $block = new Block();
        $block->setName('block.name');
        $block->setType('core.container');
        $block->setSettings([
            'name' => 'block.code',
        ]);

        $formMapper = $this->createMock(FormMapper::class, [], [], '', false);
        $formMapper->expects($this->exactly(6))->method('add');

        $service->buildCreateForm($formMapper, $block);
        $service->buildEditForm($formMapper, $block);
    }
}
