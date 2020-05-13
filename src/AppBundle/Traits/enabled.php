<?php

namespace AppBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

trait enabled
{
    /**
     * @var boolean
     * @ORM\Column(name="enabled", type="boolean", nullable=true, options={"default"=true})
     */
    protected $enabled;

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

}
