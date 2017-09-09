<?php

namespace Sonata\DashboardBundle\Tests\Fixtures\Entity;

final class FooNoGetName
{
    public function __toString()
    {
        return 'NoGetName';
    }
}
