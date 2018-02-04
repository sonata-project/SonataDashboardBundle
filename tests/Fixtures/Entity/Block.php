<?php

namespace Sonata\DashboardBundle\Tests\Fixtures\Entity;

use Sonata\DashboardBundle\Entity\BaseBlock;

class Block extends BaseBlock
{
    protected $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }
}
