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

namespace Sonata\DashboardBundle\Tests\Admin;

use PHPUnit\Framework\TestCase;
use Sonata\DashboardBundle\Admin\BlockAdmin;
use Sonata\DashboardBundle\Tests\Fixtures\Entity\FooGetName;
use Sonata\DashboardBundle\Tests\Fixtures\Entity\FooGetNameNull;
use Sonata\DashboardBundle\Tests\Fixtures\Entity\FooNoGetName;
use Symfony\Component\HttpFoundation\Request;

/**
 * BlockAdminTest.
 *
 * @author Stéphane Paté <st.pate@orange.fr>
 */
final class BlockAdminTest extends TestCase
{
    public function testToString(): void
    {
        $admin = new BlockAdmin(
            'sonata.dashboard.admin.block',
            'DashboardBundle\Entity\BaseBlock',
            'SonataDashboardBundle:BlockAdmin'
        );

        $s = new FooGetName();
        $this->assertSame('GetName', $admin->toString($s));

        $s = new FooGetNameNull();
        $this->assertSame('GetNameNull', $admin->toString($s));

        $s = new FooNoGetName();
        $this->assertSame('NoGetName', $admin->toString($s));
    }

    public function testGetPersistentParameters(): void
    {
        $admin = new BlockAdmin(
            'sonata.dashboard.admin.block',
            'DashboardBundle\Entity\BaseBlock',
            'SonataDashboardBundle:BlockAdmin'
        );

        $request = new Request();
        $admin->setRequest($request);

        $this->assertArrayHasKey('type', $admin->getPersistentParameters());
    }
}
