<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DashboardBundle\Block;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Model\Metadata;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author  Quentin Somazzi <qsomazzi@ekino.com>
 */
final class AjaxBlockService extends BaseBlockService
{
    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        return $this->renderResponse($blockContext->getTemplate(), array(
            'block_context' => $blockContext,
            'block' => $blockContext->getBlock(),
            'settings' => $blockContext->getSettings(),
        ), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $form, BlockInterface $block)
    {
        $form->add('settings', 'sonata_type_immutable_array', array(
            'label' => false,
            'keys' => array(
                array('title', 'text', array()),
                array('class', 'text', array()),
                array('url', 'text', array()),
                array('icon', 'text', array()),
                array('color', 'sonata_type_color_selector', array()),
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'template' => 'SonataDashboardBundle:Block:ajax.html.twig',
            'url' => null,
            'color' => null,
            'icon' => 'fa fa-dashboard',
            'title' => '',
            'class' => 'col-md-3 col-sm-6 col-xs-12',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata.dashboard.ajax';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata($this->getName(),
            ($code ?: $this->getName()), false, 'SonataDashboardBundle', array('class' => 'fa fa-dashboard'));
    }
}
