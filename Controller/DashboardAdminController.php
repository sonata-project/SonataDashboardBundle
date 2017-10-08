<?php

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
     * @return Response
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
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
            if ($block->getParent() === null) {
                $containers[$blockCode]['block'] = $block;
            }
        }

        $csrfProvider = $this->get('form.csrf_provider');

        return $this->render($template = $this->admin->getTemplate('compose'), [
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
     * @return Response
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
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

        return $this->render($template = $this->admin->getTemplate('compose_container_show'), [
            'blockServices' => $blockServices,
            'container' => $block,
            'dashboard' => $block->getDashboard(),
        ]);
    }
}
