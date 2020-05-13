<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Traits,
    AppBundle\Constants\GestionCongeConstant;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TypeConge
 *
 * @ORM\Table(name="type_conge")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TypeCongeRepository")
 * @ORM\HasLifecycleCallbacks();
 */
class TypeConge
{

    use Traits\id,
        Traits\createdAt,
        Traits\createdBy,
        Traits\updatedAt,
        Traits\updatedBy,
        Traits\enabled,
        Traits\libelle;
    /**
     * @var string
     *
     * @ORM\Column(name="type_conge_code", type="string", length=255, unique=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="type_conge_color", type="string", length=10, nullable=true)
     */
    private $color;

    /**
     * @var \ModeleWorkflow
     *
     * @ORM\ManyToOne(targetEntity="ModeleWorkflow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="modeleworkflow_id", referencedColumnName="id")
     * })
     */
    private $workflowType;

    /**
     * @var int
     *
     * @ORM\Column(name="type_conge_value", type="integer", nullable=true)
     */
    private $value;

    /**
     * @var \CategoryDemande
     *
     * @ORM\ManyToOne(targetEntity="CategoryDemande")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categorie_id", referencedColumnName="id")
     * })
     */
    private $category;

    /**
     * @var int
     *
     * @ORM\Column(name="imputation_type_conge", type="integer", nullable=true)
     * @Assert\NotBlank(message="Veuillez selectionner une option")
     */
    private $imputationType;

    /**
     * Set code
     *
     * @param string $code
     *
     * @return TypeConge
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Set category
     *
     * @param \AppBundle\Entity\CategoryDemande $category
     *
     * @return TypeConge
     */
    public function setCategory(\AppBundle\Entity\CategoryDemande $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\CategoryDemande
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set workflowType
     *
     * @param \AppBundle\Entity\ModeleWorkflow $workflowType
     *
     * @return TypeConge
     */
    public function setWorkflowType(\AppBundle\Entity\ModeleWorkflow $workflowType = null)
    {
        $this->workflowType = $workflowType;

        return $this;
    }

    /**
     * Get workflowType
     *
     * @return \AppBundle\Entity\ModeleWorkflow
     */
    public function getWorkflowType()
    {
        return $this->workflowType;
    }

    public function getImputationType()
    {
        return $this->imputationType;
    }

    public function setImputationType($imputationType)
    {
        $this->imputationType = $imputationType;
    }

    /**
     * is imputedSolde : congé payé
     *
     * @return boolean
     */
    public function isImputedSolde()
    {
        return ($this->imputationType == GestionCongeConstant::CONGE_PAYE);
    }

    /**
     * is permission exceptionnelle
     *
     * @return boolean
     */
    public function isPermissionExceptionnelle()
    {
        return ($this->imputationType == GestionCongeConstant::PERMISSION_EXCEPTIONNELLE);
    }

    /**
     * is permission maladie
     *
     * @return boolean
     */
    public function isPermissionMaladie()
    {
        return ($this->imputationType == GestionCongeConstant::PERMISSION_MALADIE);
    }

    /**
     * is absence non justifie maladie
     *
     * @return boolean
     */
    public function isAbsence()
    {
        return ($this->imputationType == GestionCongeConstant::ABSENCE_NON_JUSTIFIE);
    }

    /**
     * check if demande is demande RH
     * @return type
     */
    public function isDemandeRH()
    {
        return ($this->imputationType == GestionCongeConstant::DEMANDE_RH);
    }

}
