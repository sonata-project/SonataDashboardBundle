<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DashboardBundle\Block;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\Service\ContainerBlockService as BaseContainerBlockService;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Render children dashboards.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class ContainerBlockService extends BaseContainerBlockService
{
    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('enabled');

        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('code', 'text', array('required' => false)),
                array('layout', 'textarea', array()),
                array('class', 'text', array('required' => false)),
                array('template', 'sonata_type_container_template_choice', array()),
            ),
        ));

        $formMapper->add('children', 'sonata_type_collection', array(), array(
            'admin_code' => 'sonata.dashboard.admin.block',
            'edit'       => 'inline',
            'inline'     => 'table',
            'sortable'   => 'position',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'code'     => '',
            'layout'   => '{{ CONTENT }}',
            'class'    => '',
            'template' => 'SonataDashboardBundle:BlockAdmin:block_container.html.twig',
        ));
    }
}
