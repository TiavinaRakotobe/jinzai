<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Traits;

/**
 * ModeleWorkflow
 *
 * @ORM\Table(name="modele_workflow")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ModeleWorkflowRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ModeleWorkflow
{

    use Traits\id,
        Traits\libelle,
        Traits\description,
        Traits\createdAt,
        Traits\createdBy,
        Traits\updatedAt,
        Traits\updatedBy,
        Traits\enabled;
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Direction", inversedBy="modeleWorkflow")
     * @ORM\JoinTable(name="direction_modele_orkflow",
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="direction_id", referencedColumnName="id")
     *   },
     *   joinColumns={
     *     @ORM\JoinColumn(name="modele_workflow_id", referencedColumnName="id")
     *   }
     * )
     */
    private $direction;

    /**
     * @ORM\OneToMany(targetEntity="ModeleWorkflowSteps", mappedBy="modeleWorkflow", cascade={"persist", "remove"})
     */
    private $modeleWorkflowSteps;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->direction = new \Doctrine\Common\Collections\ArrayCollection();
        $this->modeleWorkflowSteps = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add direction
     *
     * @param \AppBundle\Entity\Direction $direction
     *
     * @return ModeleWorkflow
     */
    public function addDirection(\AppBundle\Entity\Direction $direction)
    {
        $this->direction[] = $direction;

        return $this;
    }

    /**
     * Remove direction
     *
     * @param \AppBundle\Entity\Direction $direction
     */
    public function removeDirection(\AppBundle\Entity\Direction $direction)
    {
        $this->direction->removeElement($direction);
    }

    /**
     * Get direction
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Add modeleWorkflowStep
     *
     * @param \AppBundle\Entity\ModeleWorkflowSteps $modeleWorkflowStep
     *
     * @return ModeleWorkflow
     */
    public function addModeleWorkflowStep(\AppBundle\Entity\ModeleWorkflowSteps $modeleWorkflowStep)
    {
        $this->modeleWorkflowSteps[] = $modeleWorkflowStep;

        return $this;
    }

    /**
     * Remove modeleWorkflowStep
     *
     * @param \AppBundle\Entity\ModeleWorkflowSteps $modeleWorkflowStep
     */
    public function removeModeleWorkflowStep(\AppBundle\Entity\ModeleWorkflowSteps $modeleWorkflowStep)
    {
        $this->modeleWorkflowSteps->removeElement($modeleWorkflowStep);
    }

    /**
     * Get modeleWorkflowSteps
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getModeleWorkflowSteps()
    {
        return $this->modeleWorkflowSteps;
    }

}
