<?php

namespace AppBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class toDate
 * @package AppBundle\Traits
 */
trait toDate
{
    /**
     * @ORM\Column(name="to_date", type="date", nullable=true)
     */
    protected $toDate;

    /**
     * @return mixed
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     * @param mixed $toDate
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;
    }

}
