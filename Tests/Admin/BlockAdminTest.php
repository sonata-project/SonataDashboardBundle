<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DashboardBundle\Tests\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\DashboardBundle\Admin\BlockAdmin;

class BlockAdminTest extends \PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $admin = new BlockAdmin('sonata.dashboard.admin.block', 'DashboardBundle\Entity\BaseBlock', 'SonataDashboardBundle:BlockAdmin');

        $s = new DummyGetName();
        $this->assertSame('GetName', $admin->toString($s));

        $s = new DummyGetNameNull();
        $this->assertSame('GetNameNull', $admin->toString($s));

        $s = new DummyNoGetName();
        $this->assertSame('NoGetName', $admin->toString($s));
    }
}

class DummyGetName
{
    public function getName()
    {
        return 'GetName';
    }
}

class DummyGetNameNull
{
    public function getName()
    {
        return;
    }

    public function __toString()
    {
        return 'GetNameNull';
    }
}

class DummyNoGetName
{
    public function __toString()
    {
        return 'NoGetName';
    }
}
