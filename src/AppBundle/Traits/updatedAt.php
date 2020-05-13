<?php

namespace AppBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

trait updatedAt
{
    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * Set updatedAt.
     *
     * @ORM\PreUpdate
     *
     * @return updatedAt
     */
    public function setUpdated()
    {
        $this->updatedAt = new \DateTime('now');

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
