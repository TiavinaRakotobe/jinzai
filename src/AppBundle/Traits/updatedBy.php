<?php

namespace AppBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Admin\UserBundle\Entity\Utilisateur;

/**
 * Class updatedBy
 * @package AppBundle\Traits
 */
trait updatedBy
{
    /**
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Admin\UserBundle\Entity\Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     * })
     */
    protected $updatedBy;

    /**
     * Set updatedBy
     *
     * @param \Admin\UserBundle\Entity\Utilisateur $updatedBy
     *
     * @return updatedBy
     */
    public function setUpdatedBy(\Admin\UserBundle\Entity\Utilisateur $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \Admin\UserBundle\Entity\Utilisateur
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

}
