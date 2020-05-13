<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Traits;
use Doctrine\ORM\EntityRepository;
use AppBundle\Constants\GestionCongeConstant;

/**
 * ModeleWorkflowSteps
 *
 * @ORM\Table(name="modele_workflow_steps")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ModeleWorkflowStepsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ModeleWorkflowSteps
{

    use Traits\id,
        Traits\libelle,
        Traits\createdAt,
        Traits\createdBy,
        Traits\updatedAt,
        Traits\updatedBy,
        Traits\enabled;
    /**
     * @var int
     *
     * @ORM\Column(name="step_order", type="integer", nullable=true, options={"default"=0})
     */
    private $stepOrder;

    /**
     * @var \ModeleWorkflow
     *
     * @ORM\ManyToOne(targetEntity="ModeleWorkflow", inversedBy="modeleWorkflowSteps")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="modele_workflow_id", referencedColumnName="id")
     * })
     */
    private $modeleWorkflow;

    /**
     * @var int
     *
     * @ORM\Column(name="step_validation_type", type="integer")
     */
    private $validationType;

    /**
     * construct
     */
    public function __construct()
    {
        $this->stepOrder = 0;
        $this->enabled = 1;
    }

    /**
     * Set stepOrder
     *
     * @param integer $stepOrder
     *
     * @return ModeleWorkflowSteps
     */
    public function setStepOrder($stepOrder)
    {
        $this->stepOrder = $stepOrder;

        return $this;
    }

    /**
     * Get stepOrder
     *
     * @return int
     */
    public function getStepOrder()
    {
        return $this->stepOrder;
    }

    /**
     * Set modeleWorkflow
     *
     * @param \AppBundle\Entity\ModeleWorkflow $modeleWorkflow
     *
     * @return ModeleWorkflowSteps
     */
    public function setModeleWorkflow(\AppBundle\Entity\ModeleWorkflow $modeleWorkflow = null)
    {
        $this->modeleWorkflow = $modeleWorkflow;

        return $this;
    }

    /**
     * Get modeleWorkflow
     *
     * @return \AppBundle\Entity\ModeleWorkflow
     */
    public function getModeleWorkflow()
    {
        return $this->modeleWorkflow;
    }

    public function getValidationType()
    {
        return $this->validationType;
    }

    public function setValidationType($validationType)
    {
        $this->validationType = $validationType;
    }

    public function isManagerValidation()
    {
        return $this->validationType == GestionCongeConstant::VALIDATION_MANAGER;
    }

    public function isRhValidation()
    {
        return $this->validationType == GestionCongeConstant::VALIDATION_RH;
    }

    public function isCustomValidation()
    {
        return $this->validationType == GestionCongeConstant::CUSTOM_VALIDATION;
    }

}
