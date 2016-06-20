<?php

/*
 * This file is part of the Sonata package.
*
* (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sonata\DashboardBundle\Twig;

use Sonata\DashboardBundle\CmsManager\CmsManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * GlobalVariables.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class GlobalVariables
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return CmsManagerInterface
     */
    public function getCmsManager()
    {
        return $this->container->get('sonata.dashboard.cms_manager_selector')->retrieve();
    }

    /**
     * @return bool
     */
    public function isEditor()
    {
        return $this->container->get('sonata.dashboard.cms_manager_selector')->isEditor();
    }

    /**
     * @return bool
     */
    public function isInlineEditionOn()
    {
        return $this->container->getParameter('sonata.dashboard.is_inline_edition_on');
    }
}
