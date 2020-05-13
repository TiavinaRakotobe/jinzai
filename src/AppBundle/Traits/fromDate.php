<?php

namespace AppBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class fromDate
 * @package AppBundle\Traits
 */
trait fromDate
{
    /**
     * @ORM\Column(name="from_date", type="date", nullable=true)
     */
    protected $fromDate;

    /**
     * @return mixed
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * @param mixed $fromDate
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;
    }

}
