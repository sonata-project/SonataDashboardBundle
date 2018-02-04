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

namespace Sonata\DashboardBundle\CmsManager;

use Sonata\DashboardBundle\Exception\DashboardNotFoundException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * This class return the correct manager instance :
 *   - sonata.dashboard.cms.dashboard if the user is an editor (ROLE_SONATA_DASHBOARD_ADMIN_DASHBOARD_EDIT)
 *   - not found exception if the user is a standard user.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class CmsManagerSelector implements CmsManagerSelectorInterface
{
    /**
     * @var CmsManagerInterface
     */
    private $cmsDashboardManager;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param CmsManagerInterface           $cmsDashboardManager
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, CmsManagerInterface $cmsDashboardManager)
    {
        $this->cmsDashboardManager = $cmsDashboardManager;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function retrieve()
    {
        if ($this->isEditor()) {
            return $this->cmsDashboardManager;
        }

        throw new DashboardNotFoundException('Unable to retrieve the cms manager');
    }

    /**
     * {@inheritdoc}
     */
    public function isEditor()
    {
        return $this->authorizationChecker->isGranted('ROLE_SONATA_DASHBOARD_ADMIN_DASHBOARD_EDIT');
    }
}
