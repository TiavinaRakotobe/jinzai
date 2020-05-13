<?php

namespace AppBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

trait validated
{
    /**
     * @var boolean
     * @ORM\Column(name="validated", type="boolean", nullable=true, options={"default"=false})
     */
    protected $validated;

    /**
     * @return boolean
     */
    public function isValidated()
    {
        return $this->validated;
    }

    /**
     * @param boolean $validated
     */
    public function setValidated($validated)
    {
        $this->validated = $validated;
    }
}
