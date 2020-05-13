<?php

namespace AppBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Admin\UserBundle\Entity\Utilisateur;

/**
 * Class createdBy
 * @package AppBundle\Traits;
 */
trait createdBy
{
    /**
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Admin\UserBundle\Entity\Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    protected $createdBy;

    /**
     * Set createdBy
     *
     * @param \Admin\UserBundle\Entity\Utilisateur $createdBy
     *
     * @return createdBy
     */
    public function setCreatedBy(\Admin\UserBundle\Entity\Utilisateur $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Admin\UserBundle\Entity\Utilisateur
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

}
