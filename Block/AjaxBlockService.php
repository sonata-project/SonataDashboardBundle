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
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Model\Metadata;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author  Quentin Somazzi <qsomazzi@ekino.com>
 */
class AjaxBlockService extends AbstractBlockService
{
    /**
     * @var string[]
     *               Colors available in AdminLTE 2.3.3 css for background with bg-xxx and bg-xxx-active
     */
    public static $colors = array(
        'red' => 'red_color',
        'yellow' => 'yellow_color',
        'aqua' => 'aqua_color',
        'blue' => 'blue_color',
        'light-blue' => 'light-blue_color',
        'green' => 'green_color',
        'navy' => 'navy_color',
        'teal' => 'teal_color',
        'olive' => 'olive_color',
        'lime' => 'lime_color',
        'orange' => 'orange_color',
        'fuchsia' => 'fuschia_color',
        'purple' => 'purple_color',
        'maroon' => 'maroon_color',
        'black' => 'black_color',
    );

    /**
     * @var string[]
     *               Each template corresponds to the AdminLTE 2.3.3 widgets
     */
    public static $templates = array(
        'SonataDashboardBundle:Block:block_ajax_simple.html.twig' => 'template.widget_simple',
        'SonataDashboardBundle:Block:block_ajax_progress.html.twig' => 'template.widget_progress',
        'SonataDashboardBundle:Block:block_ajax_link.html.twig' => 'template.widget_link',
    );

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
                array('template', 'choice', array(
                    'required' => true,
                    'label' => 'form.label_template',
                    'choices' => self::$templates,
                )),
                array('title', 'text', array(
                    'required' => false,
                    'label' => 'form.label_title',
                )),
                array('class', 'text', array(
                    'required' => false,
                    'label' => 'form.label_class',
                    'attr' => array('placeholder' => 'col-md-3 col-sm-6 col-xs-12'),
                )),
                array('url', 'url', array(
                    'required' => false,
                    'label' => 'form.label_url',
                    'attr' => array('placeholder' => 'http://'),
                )),
                array('icon', 'text', array(
                    'required' => false,
                    'label' => 'form.label_icon',
                    'attr' => array('placeholder' => 'fa fa-dashboard'),
                )),
                array('color', 'choice', array(
                     'required' => true,
                     'label' => 'form.label_color',
                     'choices' => self::$colors,
                )),
                array('progress', 'percent', array(
                     'required' => false,
                     'label' => 'form.label_progress',
                )),
                array('description', 'text', array(
                     'required' => false,
                     'label' => 'form.label_description',
                )),
                array('link', 'url', array(
                     'required' => false,
                     'label' => 'form.label_link',
                     'attr' => array('placeholder' => 'http://'),
                )),
            ),
            'translation_domain' => 'SonataDashboardBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'template' => 'SonataDashboardBundle:Block:block_ajax_simple.html.twig',
            'title' => '',
            'class' => 'col-md-3 col-sm-6 col-xs-12',
            'url' => null,
            'icon' => 'fa fa-dashboard',
            'color' => null,
            'progress' => 0,
            'description' => '',
            'link' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata.dashboard.block.ajax';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata($this->getName(), (!is_null($code) ? $code : $this->getName()), false, 'SonataDashboardBundle', array(
            'class' => 'fa fa-dashboard',
        ));
    }
}
