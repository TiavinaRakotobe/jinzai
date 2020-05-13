<?php

namespace AppBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class validatedAt
 * @package AppBundle\Traits
 */
trait validatedAt
{
    /**
     * @ORM\Column(name="validated_at", type="datetime", nullable=true)
     */
    protected $validatedAt;

    /**
     * Set validatedAt.
     *
     * @return validatedAt
     */
    public function setValidatedAt()
    {
        $this->validatedAt = new \DateTime('now');

        return $this;
    }

    /**
     * Get validatedAt.
     *
     * @return \DateTime
     */
    public function getValidatedAt()
    {
        return $this->validatedAt;
    }
}
