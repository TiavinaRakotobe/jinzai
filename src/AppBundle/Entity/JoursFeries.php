<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Traits;

/**
 * JoursFeries
 *
 * @ORM\Table(name="jours_feries")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JoursFeriesRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class JoursFeries
{

    use Traits\id,
        Traits\createdAt,
        Traits\createdBy,
        Traits\updatedAt,
        Traits\updatedBy,
        Traits\enabled,
        Traits\description;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="jours_feries_date", type="datetime")
     */
    private $joursFeriesDate;

    /**
     * Set joursFeriesDate
     *
     * @param \DateTime $joursFeriesDate
     *
     * @return JoursFeries
     */
    public function setJoursFeriesDate($joursFeriesDate)
    {
        $this->joursFeriesDate = $joursFeriesDate;

        return $this;
    }

    /**
     * Get joursFeriesDate
     *
     * @return \DateTime
     */
    public function getJoursFeriesDate()
    {
        return $this->joursFeriesDate;
    }

}
