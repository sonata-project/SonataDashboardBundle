<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DashboardBundle\Twig\Extension;

use Sonata\BlockBundle\Templating\Helper\BlockHelper;
use Sonata\DashboardBundle\CmsManager\CmsManagerSelectorInterface;
use Sonata\DashboardBundle\Model\DashboardBlockInterface;

/**
 * DashboardExtension.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class DashboardExtension extends \Twig_Extension implements \Twig_Extension_InitRuntimeInterface
{
    /**
     * @var CmsManagerSelectorInterface
     */
    private $cmsManagerSelector;

    /**
     * @var BlockHelper
     */
    private $blockHelper;

    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * @var array
     */
    private $resources;

    /**
     * Constructor.
     *
     * @param CmsManagerSelectorInterface $cmsManagerSelector A CMS manager selector
     * @param BlockHelper                 $blockHelper        The Block Helper
     */
    public function __construct(CmsManagerSelectorInterface $cmsManagerSelector, BlockHelper $blockHelper)
    {
        $this->cmsManagerSelector = $cmsManagerSelector;
        $this->blockHelper = $blockHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('sonata_dashboard_render_container', array($this, 'renderContainer'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('sonata_dashboard_render_block', array($this, 'renderBlock'), array('is_safe' => array('html'))),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_dashboard';
    }

    /**
     * @param string $name
     * @param null   $dashboard
     * @param array  $options
     *
     * @return Response
     */
    public function renderContainer($name, $dashboard = null, array $options = array())
    {
        $cms = $this->cmsManagerSelector->retrieve();

        if (!$dashboard) {
            return '';
        }

        $container = $cms->findContainer($name, $dashboard);

        if (!$container) {
            return '';
        }

        return $this->renderBlock($container, $options);
    }

    /**
     * @param DashboardBlockInterface $block
     * @param array                   $options
     *
     * @return string
     */
    public function renderBlock(DashboardBlockInterface $block, array $options = array())
    {
        if ($block->getEnabled() === false && !$this->cmsManagerSelector->isEditor()) {
            return '';
        }

        // build the parameters array
        $options = array_merge(array(
            'use_cache' => isset($options['use_cache']) ? $options['use_cache'] : true,
            'extra_cache_keys' => array(),
        ), $options);

        return $this->blockHelper->render($block, $options);
    }

    /**
     * @param string $template
     * @param array  $parameters
     *
     * @return string
     */
    private function render($template, array $parameters = array())
    {
        if (!isset($this->resources[$template])) {
            $this->resources[$template] = $this->environment->loadTemplate($template);
        }

        return $this->resources[$template]->render($parameters);
    }
}
