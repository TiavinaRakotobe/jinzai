<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Traits;

/**
 * Holiday
 *
 * @ORM\Table(name="holiday")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HolidayRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Holiday
{

    use Traits\id,
        Traits\createdAt,
        Traits\createdBy,
        Traits\updatedAt,
        Traits\updatedBy,
        Traits\enabled,
        Traits\description;
    /**
     * @var string
     *
     * @ORM\Column(name="holiday_day", type="string", length=2, nullable=false)
     */
    private $holidayDay;

    /**
     * @var string
     *
     * @ORM\Column(name="holiday_month", type="string", length=2, nullable=false)
     */
    private $holidayMonth;

    /**
     * Set holidayDay
     *
     * @param string $holidayDay
     *
     * @return Holiday
     */
    public function setHolidayDay($holidayDay)
    {
        $this->holidayDay = $holidayDay;

        return $this;
    }

    /**
     * Get holidayDay
     *
     * @return string
     */
    public function getHolidayDay()
    {
        return $this->holidayDay;
    }

    /**
     * Set holidayMonth
     *
     * @param string $holidayMonth
     *
     * @return Holiday
     */
    public function setHolidayMonth($holidayMonth)
    {
        $this->holidayMonth = $holidayMonth;

        return $this;
    }

    /**
     * Get holidayMonth
     *
     * @return string
     */
    public function getHolidayMonth()
    {
        return $this->holidayMonth;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Holiday
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

}
