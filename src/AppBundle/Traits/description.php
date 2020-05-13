<?php

namespace AppBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class description
 * @package AppBundle\Traits
 */
trait description
{
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}
