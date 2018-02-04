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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Dashboard Admin Controller.
 *
 * @author Quentin Somazzi <qsomazzi@ekino.com>
 */
class DashboardAdminController extends CRUDController
{
    /**
     * @param Request $request
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function composeAction(Request $request)
    {
        $this->admin->checkAccess('compose');
        if (false === $this->get('sonata.dashboard.admin.block')->isGranted('LIST')) {
            throw $this->createAccessDeniedException();
        }

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

        $csrfProvider = $this->get('form.csrf_provider');

        return $this->render($this->admin->getTemplate('compose'), [
            'object' => $dashboard,
            'action' => 'edit',
            'containers' => $containers,
            'csrfTokens' => [
                'remove' => $csrfProvider->generateCsrfToken('sonata.delete'),
            ],
        ]);
    }

    /**
     * @param Request $request
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function composeContainerShowAction(Request $request)
    {
        if (false === $this->get('sonata.dashboard.admin.block')->isGranted('LIST')) {
            throw $this->createAccessDeniedException();
        }

        $id = $request->get($this->admin->getIdParameter());
        $block = $this->get('sonata.dashboard.admin.block')->getObject($id);
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
     * @param Request $request
     *
     * @throws AccessDeniedException
     *
     * @return Response
     */
    public function renderAction(Request $request = null)
    {
        $this->admin->checkAccess('render');

        if ($this->get('sonata.dashboard.admin.block')->isGranted('LIST')) {
            throw $this->createAccessDeniedException();
        }

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
            [], ['updatedAt' => 'DESC'], 5
        );

        $csrfProvider = $this->get('form.csrf_provider');

        return $this->render($this->admin->getTemplate('render'), [
            'object' => $dashboard,
            'default' => $default,
            'action' => 'edit',
            'containers' => $containers,
            'dashboards' => $dashboards,
            'csrfTokens' => [
                'remove' => $csrfProvider->generateCsrfToken('sonata.delete'),
            ],
        ]);
    }
}
