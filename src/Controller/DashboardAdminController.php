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

namespace Sonata\DashboardBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\DashboardBundle\Admin\BlockAdmin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Dashboard Admin Controller.
 *
 * @author Quentin Somazzi <qsomazzi@ekino.com>
 */
final class DashboardAdminController extends CRUDController
{
    /**
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function composeAction(Request $request): Response
    {
        $this->admin->checkAccess('compose');

        $this->getBlockAdmin()->checkAccess('list');

        $id = $request->get($this->admin->getIdParameter());
        $dashboard = $this->admin->getObject($id);
        if (!$dashboard) {
            throw $this->createNotFoundException(sprintf('unable to find the dashboard with id : %s', $id));
        }

        $containers = [];

        // separate containers.
        foreach ($dashboard->getBlocks() as $block) {
            $blockCode = $block->getSetting('code');
            if (null === $block->getParent()) {
                $containers[$blockCode]['block'] = $block;
            }
        }

        return $this->render($this->admin->getTemplate('compose'), [
            'object' => $dashboard,
            'action' => 'edit',
            'containers' => $containers,
            'csrfTokens' => [
                'remove' => $this->getCsrfToken('sonata.dashboard'),
            ],
        ]);
    }

    /**
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function composeContainerShowAction(Request $request): Response
    {
        $this->getBlockAdmin()->checkAccess('list');

        $id = $request->get($this->admin->getIdParameter());

        $block = $this->getBlockAdmin()->getObject($id);
        if (!$block) {
            throw $this->createNotFoundException(sprintf('unable to find the block with id : %s', $id));
        }

        $blockServices = $this->get('sonata.block.manager')->getServicesByContext('sonata_dashboard_bundle', false);

        return $this->render($this->admin->getTemplate('compose_container_show'), [
            'blockServices' => $blockServices,
            'container' => $block,
            'dashboard' => $block->getDashboard(),
        ]);
    }

    /**
     * @throws AccessDeniedException
     */
    public function renderAction(Request $request): Response
    {
        $this->admin->checkAccess('render');

        $this->getBlockAdmin()->checkAccess('list');

        // true when renders default dashboard from sonata_admin_dashboard redirect
        $default = $request->query->get('default');
        $id = $request->get($this->admin->getIdParameter());
        $dashboard = $this->admin->getObject($id);
        if (!$dashboard) {
            throw $this->createNotFoundException(sprintf('unable to find the dashboard with id : %s', $id));
        }

        $containers = [];

        // separate containers
        foreach ($dashboard->getBlocks() as $block) {
            $blockCode = $block->getSetting('code');
            if (null === $block->getParent()) {
                $containers[$blockCode] = $block;
            }
        }

        $dashboards = $this->get('sonata.dashboard.manager.dashboard')->findBy(
            [],
            ['updatedAt' => 'DESC'],
            5
        );

        return $this->render($this->admin->getTemplate('render'), [
            'object' => $dashboard,
            'default' => $default,
            'action' => 'edit',
            'containers' => $containers,
            'dashboards' => $dashboards,
            'csrfTokens' => [
                'remove' => $this->getCsrfToken('sonata.dashboard'),
            ],
        ]);
    }

    private function getBlockAdmin(): BlockAdmin
    {
        return $this->get('sonata.dashboard.admin.block');
    }
}
