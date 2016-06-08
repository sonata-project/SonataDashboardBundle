<?php

namespace Sonata\DashboardBundle\Tests\Fixtures\Entity;

final class FooGetNameNull
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
