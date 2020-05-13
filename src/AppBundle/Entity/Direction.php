<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Traits;

/**
 * Direction
 *
 * @ORM\Table(name="direction")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DirectionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Direction
{

    use Traits\id,
        Traits\libelle,
        Traits\createdAt,
        Traits\createdBy,
        Traits\updatedAt,
        Traits\updatedBy,
        Traits\enabled;
    /**
     * @var string
     *
     * @ORM\Column(name="direction_sigle", type="string", length=255, unique=true)
     */
    private $sigle;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="ModeleWorkflow", mappedBy="direction")
     *
     */
    private $modeleWorkflow;

    /**
     * Set sigle
     *
     * @param string $sigle
     *
     * @return Direction
     */
    public function setSigle($sigle)
    {
        $this->sigle = $sigle;

        return $this;
    }

    /**
     * Get sigle
     *
     * @return string
     */
    public function getSigle()
    {
        return $this->sigle;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->modeleWorkflow = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add modeleWorkflow
     *
     * @param \AppBundle\Entity\Modeleworkflow $modeleWorkflow
     *
     * @return Direction
     */
    public function addModeleWorkflow(\AppBundle\Entity\Modeleworkflow $modeleWorkflow)
    {
        $this->modeleWorkflow[] = $modeleWorkflow;

        return $this;
    }

    /**
     * Remove modeleWorkflow
     *
     * @param \AppBundle\Entity\Modeleworkflow $modeleWorkflow
     */
    public function removeModeleWorkflow(\AppBundle\Entity\Modeleworkflow $modeleWorkflow)
    {
        $this->modeleWorkflow->removeElement($modeleWorkflow);
    }

    /**
     * Get modeleWorkflow
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getModeleWorkflow()
    {
        return $this->modeleWorkflow;
    }

}
