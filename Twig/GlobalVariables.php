<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DashboardBundle\Twig;

use Sonata\DashboardBundle\CmsManager\CmsManagerSelectorInterface;

/**
 * GlobalVariables.
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
final class GlobalVariables
{
    /**
     * @var ContainerInterface
     */
    private $cmsManagerSelector;

    /**
     * @param CmsManagerSelector $cmsManagerSelector
     */
    public function __construct(CmsManagerSelectorInterface $cmsManagerSelector)
    {
        $this->cmsManagerSelector = $cmsManagerSelector;
    }

    /**
     * @return CmsManagerInterface
     */
    public function getCmsManager()
    {
        return $this->cmsManagerSelector->retrieve();
    }

    /**
     * @return bool
     */
    public function isEditor()
    {
        return $this->cmsManagerSelector->isEditor();
    }
}
