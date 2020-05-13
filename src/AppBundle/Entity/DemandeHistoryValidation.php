<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Traits;

/**
 * DemandeHistoryValidation
 *
 * @ORM\Table(name="demande_history_validation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DemandeHistoryValidationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class DemandeHistoryValidation
{

    use Traits\id,
        Traits\createdAt,
        Traits\createdBy,
        Traits\updatedAt,
        Traits\updatedBy;
    /**
     * @var \DemandeConge
     *
     * @ORM\ManyToOne(targetEntity="DemandeConge", inversedBy="demandeHistoryValidation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="demande_conge_id", referencedColumnName="id")
     * })
     */
    private $demandeConge;

    /**
     * @var \ModeleWorkflowSteps
     *
     * @ORM\ManyToOne(targetEntity="ModeleWorkflowSteps")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="modele_workflow_step_id", referencedColumnName="id")
     * })
     */
    private $modeleWorkflowStep;

    /**
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Admin\UserBundle\Entity\Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="validator_id", referencedColumnName="id")
     * })
     */
    private $validator;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="text", nullable=true)
     */
    private $commentaire;

    /**
     * @var int
     *
     * @ORM\Column(name="validation_status", type="integer", nullable=true, options={"default":0})
     */
    private $validationStatus;

    /**
     * construct
     */
    public function __construct()
    {
        $this->validationStatus = 0;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return DemandeHistoryValidation
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set validationStatus
     *
     * @param integer $validationStatus
     *
     * @return DemandeHistoryValidation
     */
    public function setValidationStatus($validationStatus)
    {
        $this->validationStatus = $validationStatus;

        return $this;
    }

    /**
     * Get validationStatus
     *
     * @return integer
     */
    public function getValidationStatus()
    {
        return $this->validationStatus;
    }

    /**
     * Set demandeConge
     *
     * @param \AppBundle\Entity\DemandeConge $demandeConge
     *
     * @return DemandeHistoryValidation
     */
    public function setDemandeConge(\AppBundle\Entity\DemandeConge $demandeConge = null)
    {
        $this->demandeConge = $demandeConge;

        return $this;
    }

    /**
     * Get demandeConge
     *
     * @return \AppBundle\Entity\DemandeConge
     */
    public function getDemandeConge()
    {
        return $this->demandeConge;
    }

    /**
     * Set modeleWorkflowStep
     *
     * @param \AppBundle\Entity\ModeleWorkflowSteps $modeleWorkflowStep
     *
     * @return DemandeHistoryValidation
     */
    public function setModeleWorkflowStep(\AppBundle\Entity\ModeleWorkflowSteps $modeleWorkflowStep = null)
    {
        $this->modeleWorkflowStep = $modeleWorkflowStep;

        return $this;
    }

    /**
     * Get modeleWorkflowStep
     *
     * @return \AppBundle\Entity\ModeleWorkflowSteps
     */
    public function getModeleWorkflowStep()
    {
        return $this->modeleWorkflowStep;
    }

    /**
     * Set validator
     *
     * @param \Admin\UserBundle\Entity\Utilisateur $validator
     *
     * @return DemandeHistoryValidation
     */
    public function setValidator(\Admin\UserBundle\Entity\Utilisateur $validator = null)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Get validator
     *
     * @return \Admin\UserBundle\Entity\Utilisateur
     */
    public function getValidator()
    {
        return $this->validator;
    }

}
