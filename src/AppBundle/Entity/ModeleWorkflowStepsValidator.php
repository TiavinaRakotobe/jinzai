<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Traits;

/**
 * ModeleWorkflowStepsValidator
 *
 * @ORM\Table(name="modele_workflow_steps_validator")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ModeleWorkflowStepsValidatorRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ModeleWorkflowStepsValidator
{

    use Traits\id,
        Traits\createdAt,
        Traits\createdBy,
        Traits\updatedAt,
        Traits\updatedBy,
        Traits\enabled;
    /**
     * @var \ModeleWorkflowSteps
     *
     * @ORM\ManyToOne(targetEntity="ModeleWorkflowSteps", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="modele_workflow_steps_id", referencedColumnName="id")
     * })
     */
    private $modeleWorkflowStepsId;

    /**
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Admin\UserBundle\Entity\Utilisateur", inversedBy="wfStepValidator")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="validator_id", referencedColumnName="id")
     * })
     */
    private $validator;

    /**
     * Set modeleWorkflowStepsId
     *
     * @param \AppBundle\Entity\ModeleWorkflowSteps $modeleWorkflowStepsId
     *
     * @return ModeleWorkflowStepsValidator
     */
    public function setModeleWorkflowStepsId(\AppBundle\Entity\ModeleWorkflowSteps $modeleWorkflowStepsId = null)
    {
        $this->modeleWorkflowStepsId = $modeleWorkflowStepsId;

        return $this;
    }

    /**
     * Get modeleWorkflowStepsId
     *
     * @return \AppBundle\Entity\ModeleWorkflowSteps
     */
    public function getModeleWorkflowStepsId()
    {
        return $this->modeleWorkflowStepsId;
    }

    /**
     * Set validator
     *
     * @param \Admin\UserBundle\Entity\Utilisateur $validator
     *
     * @return ModeleWorkflowStepsValidator
     */
    public function setValidator(\Admin\UserBundle\Entity\Utilisateur $validator = null)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Get validatorId
     *
     * @return \Admin\UserBundle\Entity\Utilisateur
     */
    public function getValidator()
    {
        return $this->validator;
    }

}
