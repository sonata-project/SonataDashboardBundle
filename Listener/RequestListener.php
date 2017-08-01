<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DashboardBundle\Listener;

use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * This class redirect the onKernelRequest event
 * to the correct DashboardAdminController action.
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
final class RequestListener
{
    /**
     * @var AdminInterface
     */
    private $admin;

    /**
     * Constructor.
     *
     * @param AdminInterface $admin Dashboard admin
     */
    public function __construct(AdminInterface $admin)
    {
        $this->admin = $admin;
    }

    /**
     * Filter the `kernel.request` event to catch the dashboardAction.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequest()->get('_route') == 'sonata_admin_dashboard') {
            $modelManager = $this->admin->getModelManager();

            $defaultDashboard = $modelManager->findOneBy(
                $this->admin->getClass(),
                array('isDefault' => true, 'enabled' => true)
            );

            if ($defaultDashboard) {
                $url = $this->admin->generateObjectUrl('render', $defaultDashboard, array('default' => true));
                $event->setResponse(new RedirectResponse($url));
            }
        }
    }
}
