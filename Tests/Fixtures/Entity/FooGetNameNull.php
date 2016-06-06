<?php

namespace Sonata\DashboardBundle\Tests\Fixtures\Entity;

class FooGetNameNull
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
