<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DashboardBundle\Tests\Block;

use Sonata\BlockBundle\Block\BlockContext;
use Sonata\BlockBundle\Model\Block;
use Sonata\BlockBundle\Test\AbstractBlockServiceTestCase;
use Sonata\DashboardBundle\Block\AjaxBlockService;

class AjaxBlockServiceTest extends AbstractBlockServiceTestCase
{
    public function testExecute()
    {
        $block = new Block();

        $block->setName('block.name');
        $block->setType('sonata.dashboard.block.ajax');
        $block->setSettings(array(
            'code' => 'block.code',
        ));
        $blockContext = new BlockContext($block, array('template' => 'SonataDashboardBundle:Block:block_ajax.html.twig'));

        $blockService = new AjaxBlockService('sonata.dashboard.block.ajax', $this->templating);
        $blockService->execute($blockContext);

        $this->assertEquals('SonataDashboardBundle:Block:block_ajax.html.twig', $this->templating->view);
        $this->assertEquals('block.code', $this->templating->parameters['block']->getSetting('code'));
        $this->assertEquals('block.name', $this->templating->parameters['block']->getName());
        $this->assertInstanceOf('Sonata\BlockBundle\Model\Block', $this->templating->parameters['block']);
    }
}
