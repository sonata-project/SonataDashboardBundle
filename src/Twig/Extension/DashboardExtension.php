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

namespace Sonata\DashboardBundle\Twig\Extension;

use Sonata\BlockBundle\Templating\Helper\BlockHelper;
use Sonata\DashboardBundle\CmsManager\CmsManagerSelectorInterface;
use Sonata\DashboardBundle\Model\DashboardBlockInterface;
use Sonata\DashboardBundle\Model\DashboardInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * DashboardExtension.
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
final class DashboardExtension extends AbstractExtension
{
    /**
     * @var CmsManagerSelectorInterface
     */
    private $cmsManagerSelector;

    /**
     * @var BlockHelper
     */
    private $blockHelper;

    public function __construct(CmsManagerSelectorInterface $cmsManagerSelector, BlockHelper $blockHelper)
    {
        $this->cmsManagerSelector = $cmsManagerSelector;
        $this->blockHelper = $blockHelper;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('sonata_dashboard_render_container', [$this, 'renderContainer'], ['is_safe' => ['html']]),
            new TwigFunction('sonata_dashboard_render_block', [$this, 'renderBlock'], ['is_safe' => ['html']]),
        ];
    }

    public function getName()
    {
        return 'sonata_dashboard';
    }

    public function renderContainer(string $name, ?DashboardInterface $dashboard = null, array $options = []): ?string
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

    public function renderBlock(DashboardBlockInterface $block, array $options = []): ?string
    {
        if (false === $block->getEnabled() && !$this->cmsManagerSelector->isEditor()) {
            return '';
        }

        // build the parameters array
        $options = array_merge([
            'use_cache' => $options['use_cache'] ?? true,
            'extra_cache_keys' => [],
        ], $options);

        return $this->blockHelper->render($block, $options);
    }
}
