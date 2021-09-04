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

namespace Sonata\DashboardBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Sonata\DashboardBundle\Tests\Model\Dashboard;

final class DashboardTest extends TestCase
{
    public function testGetterSetter(): void
    {
        $dashboard = new Dashboard();
        $dashboard->setEnabled(true);
        static::assertTrue($dashboard->getEnabled());

        $datetime = new \DateTime();
        $dashboard->setCreatedAt($datetime);
        $dashboard->setUpdatedAt($datetime);
        static::assertSame($datetime, $dashboard->getCreatedAt());
        static::assertSame($datetime, $dashboard->getUpdatedAt());

        $dashboard->setName(null);
        static::assertSame('-', (string) $dashboard);
        $dashboard->setName('Salut');
        static::assertSame('Salut', (string) $dashboard);
    }
}
