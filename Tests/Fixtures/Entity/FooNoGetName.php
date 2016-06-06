<?php

namespace Sonata\DashboardBundle\Tests\Fixtures\Entity;

class FooNoGetName
{
    public function __toString()
    {
        return 'NoGetName';
    }
}
