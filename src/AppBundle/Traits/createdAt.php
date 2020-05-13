<?php

namespace AppBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class createdAt
 * @package AppBundle\Traits
 */
trait createdAt
{
    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * Set createdAt.
     *
     * @ORM\PrePersist
     *
     * @return createdAt
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime('now');

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
