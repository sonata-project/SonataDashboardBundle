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

        $formMapper->add('settings', 'sonata_type_immutable_array', [
            'keys' => [
                ['code', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                    'required' => false,
                    'label' => 'form.label_code',
                ]],
                ['layout', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', [
                    'label' => 'form.label_layout',
                ]],
                ['class', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                    'required' => false,
                    'label' => 'form.label_class',
                ]],
                ['template', 'Sonata\BlockBundle\Form\Type\ContainerTemplateType', [
                    'label' => 'form.label_template',
                ]],
            ],
            'translation_domain' => 'SonataDashboardBundle',
        ]);

        $formMapper->add('children', 'sonata_type_collection', [], [
            'admin_code' => 'sonata.dashboard.admin.block',
            'edit' => 'inline',
            'inline' => 'table',
            'sortable' => 'position',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'code' => '',
            'layout' => '{{ CONTENT }}',
            'class' => '',
            'color' => '',
            'template' => 'SonataDashboardBundle:BlockAdmin:block_container.html.twig',
        ]);
    }
}
