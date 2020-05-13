<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Traits;
use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Component\Validator\ExecutionContextInterface;

/**
 * DemandeConge
 *
 * @ORM\Table(name="demande_conge")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DemandeCongeRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Assert\Callback(methods={"checkTotalDays"})
 */
class DemandeConge
{

    use Traits\id,
        Traits\createdAt,
        Traits\createdBy,
        Traits\updatedAt,
        Traits\updatedBy;
    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=255, nullable=true)
     * @--Assert\NotBlank(message="Champ obligatoire")
     */
    private $numero;

    /**
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Admin\UserBundle\Entity\Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="demandor_id", referencedColumnName="id")
     * })
     */
    private $demandor;

    /**
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Admin\UserBundle\Entity\Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="initiator_id", referencedColumnName="id")
     * })
     */
    private $initiator;

    /**
     * @var TypeConge
     *
     * @ORM\ManyToOne(targetEntity="ModeleWorkflow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="modele_workflow_id", referencedColumnName="id")
     * })
     */
    //private $modeleWorkflow;

    /**
     * @ORM\OneToMany(targetEntity="DemandeHistoryValidation", mappedBy="demandeConge", cascade={"persist", "remove"})
     */
    private $demandeHistoryValidation;

    /**
     * @var TypeConge
     *
     * @ORM\ManyToOne(targetEntity="TypeConge")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_conge_id", referencedColumnName="id")
     * })
     */
    private $typeConge;

    /**
     * @var \DateTime
     * @Assert\NotBlank(message="Champ obligatoire")
     * @ORM\Column(name="date_start", type="date")
     */
    private $dateStart;

    /**
     * @var bool
     *
     * @ORM\Column(name="date_start_morning", type="boolean", nullable=true)
     */
    private $dateStartMorning;

    /**
     * @var bool
     *
     * @ORM\Column(name="date_start_afternoon", type="boolean", nullable=true)
     */
    private $dateStartAfternoon;

    /**
     * @var \DateTime
     * @Assert\NotBlank(message="Champ obligatoire")
     * @ORM\Column(name="date_end", type="date")
     */
    private $dateEnd;

    /**
     * @var bool
     *
     * @ORM\Column(name="date_end_morning", type="boolean", nullable=true)
     */
    private $dateEndMorning;

    /**
     * @var bool
     *
     * @ORM\Column(name="date_end_afternoon", type="boolean", nullable=true)
     */
    private $dateEndAfternoon;

    /**
     * @var float
     * @Assert\NotBlank(message="Champ obligatoire")
     * @ORM\Column(name="total_days", type="float", options={"default"=0})
     */
    private $totalDays;

    /**
     * @var \DateTime
     * @Assert\NotBlank(message="Champ obligatoire")
     * @ORM\Column(name="date_retour", type="date")
     */
    private $dateRetour;

    /**
     * @var int
     *
     * @ORM\Column(name="demande_status", type="integer")
     */
    private $demandeStatus;
	
	/**
     * @var string $observations
     *
     * @ORM\Column(name="observations", type="text", nullable=true)
     */
    private $observations;


    /**
     * @var int
     *
     * @ORM\Column(name="increment_numnber", type="integer", nullable=true)
     */
    private $incrementNumber;

    /**
     * @ORM\OneToMany(targetEntity="Interim", mappedBy="demandeConge", cascade={"persist", "remove"})
     */
    private $interim;

    /**
     * @ORM\OneToMany(targetEntity="Document", mappedBy="demandeConge", cascade={"persist", "remove"})
     */
    private $document;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->demandeStatus = 0;
        $this->demandeHistoryValidation = new \Doctrine\Common\Collections\ArrayCollection();
        $this->interim = new \Doctrine\Common\Collections\ArrayCollection();
        $this->document = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set numero
     *
     * @param string $numero
     *
     * @return DemandeConge
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set dateStart
     *
     * @param \DateTime $dateStart
     *
     * @return DemandeConge
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get dateStart
     *
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set dateStartMorning
     *
     * @param boolean $dateStartMorning
     *
     * @return DemandeConge
     */
    public function setDateStartMorning($dateStartMorning)
    {
        $this->dateStartMorning = $dateStartMorning;

        return $this;
    }

    /**
     * Get dateStartMorning
     *
     * @return boolean
     */
    public function getDateStartMorning()
    {
        return $this->dateStartMorning;
    }

    /**
     * Set dateStartAfternoon
     *
     * @param boolean $dateStartAfternoon
     *
     * @return DemandeConge
     */
    public function setDateStartAfternoon($dateStartAfternoon)
    {
        $this->dateStartAfternoon = $dateStartAfternoon;

        return $this;
    }

    /**
     * Get dateStartAfternoon
     *
     * @return boolean
     */
    public function getDateStartAfternoon()
    {
        return $this->dateStartAfternoon;
    }

    /**
     * Set dateEnd
     *
     * @param \DateTime $dateEnd
     *
     * @return DemandeConge
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd
     *
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Set dateEndMorning
     *
     * @param boolean $dateEndMorning
     *
     * @return DemandeConge
     */
    public function setDateEndMorning($dateEndMorning)
    {
        $this->dateEndMorning = $dateEndMorning;

        return $this;
    }

    /**
     * Get dateEndMorning
     *
     * @return boolean
     */
    public function getDateEndMorning()
    {
        return $this->dateEndMorning;
    }

    /**
     * Set dateEndAfternoon
     *
     * @param boolean $dateEndAfternoon
     *
     * @return DemandeConge
     */
    public function setDateEndAfternoon($dateEndAfternoon)
    {
        $this->dateEndAfternoon = $dateEndAfternoon;

        return $this;
    }

    /**
     * Get dateEndAfternoon
     *
     * @return boolean
     */
    public function getDateEndAfternoon()
    {
        return $this->dateEndAfternoon;
    }

    /**
     * Set totalDays
     *
     * @param float $totalDays
     *
     * @return DemandeConge
     */
    public function setTotalDays($totalDays)
    {
        $this->totalDays = $totalDays;

        return $this;
    }

    /**
     * Get totalDays
     *
     * @return float
     */
    public function getTotalDays()
    {
        return $this->totalDays;
    }

    /**
     * Set dateRetour
     *
     * @param \DateTime $dateRetour
     *
     * @return DemandeConge
     */
    public function setDateRetour($dateRetour)
    {
        $this->dateRetour = $dateRetour;

        return $this;
    }

    /**
     * Get dateRetour
     *
     * @return \DateTime
     */
    public function getDateRetour()
    {
        return $this->dateRetour;
    }

    /**
     * Set demandeStatus
     *
     * @param integer $demandeStatus
     *
     * @return DemandeConge
     */
    public function setDemandeStatus($demandeStatus)
    {
        $this->demandeStatus = $demandeStatus;

        return $this;
    }

    /**
     * Get demandeStatus
     *
     * @return integer
     */
    public function getDemandeStatus()
    {
        return $this->demandeStatus;
    }
	
	/**
     * Set observations
     *
     * @param string $observations
     *
     * @return Demandeconge
     */
    public function setObservations($observations)
    {
        $this->observations = $observations;

        return $this;
    }

    /**
     * Get observations
     *
     * @return string
     */
    public function getObservations()
    {
        return $this->observations;
    }


    /**
     * Set demandor
     *
     * @param \Admin\UserBundle\Entity\Utilisateur $demandor
     *
     * @return DemandeConge
     */
    public function setDemandor(\Admin\UserBundle\Entity\Utilisateur $demandor = null)
    {
        $this->demandor = $demandor;

        return $this;
    }

    /**
     * Get demandor
     *
     * @return \Admin\UserBundle\Entity\Utilisateur
     */
    public function getDemandor()
    {
        return $this->demandor;
    }

    /**
     * Set initiator
     *
     * @param \Admin\UserBundle\Entity\Utilisateur $initiator
     *
     * @return DemandeConge
     */
    public function setInitiator(\Admin\UserBundle\Entity\Utilisateur $initiator = null)
    {
        $this->initiator = $initiator;

        return $this;
    }

    /**
     * Get initiator
     *
     * @return \Admin\UserBundle\Entity\Utilisateur
     */
    public function getInitiator()
    {
        return $this->initiator;
    }

    /**
     * Set typeConge
     *
     * @param \AppBundle\Entity\TypeConge $typeConge
     *
     * @return DemandeConge
     */
    public function setTypeConge(\AppBundle\Entity\TypeConge $typeConge = null)
    {
        $this->typeConge = $typeConge;

        return $this;
    }

    /**
     * Get typeConge
     *
     * @return \AppBundle\Entity\TypeConge
     */
    public function getTypeConge()
    {
        return $this->typeConge;
    }

    /**
     * Set modeleWorkflow
     *
     * @param \AppBundle\Entity\ModeleWorkflow $modeleWorkflow
     *
     * @return DemandeConge
     */
    /* public function setModeleWorkflow(\AppBundle\Entity\ModeleWorkflow $modeleWorkflow = null)
      {
      $this->modeleWorkflow = $modeleWorkflow;

      return $this;
      } */

    /**
     * Get modeleWorkflow
     *
     * @return \AppBundle\Entity\ModeleWorkflow
     */
    /* public function getModeleWorkflow()
      {
      return $this->modeleWorkflow;
      } */

    /**
     * Add demandeHistoryValidation
     *
     * @param \AppBundle\Entity\DemandeHistoryValidation $demandeHistoryValidation
     *
     * @return DemandeConge
     */
    public function addDemandeHistoryValidation(\AppBundle\Entity\DemandeHistoryValidation $demandeHistoryValidation)
    {
        $this->demandeHistoryValidation[] = $demandeHistoryValidation;

        return $this;
    }

    /**
     * Remove demandeHistoryValidation
     *
     * @param \AppBundle\Entity\DemandeHistoryValidation $demandeHistoryValidation
     */
    public function removeDemandeHistoryValidation(\AppBundle\Entity\DemandeHistoryValidation $demandeHistoryValidation)
    {
        $this->demandeHistoryValidation->removeElement($demandeHistoryValidation);
    }

    /**
     * Get demandeHistoryValidation
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDemandeHistoryValidation()
    {
        return $this->demandeHistoryValidation;
    }

    public function getIncrementNumber()
    {
        return $this->incrementNumber;
    }

    public function setIncrementNumber($incrementNumber)
    {
        $this->incrementNumber = $incrementNumber;
    }

    /**
     * Add interim
     *
     * @param \AppBundle\Entity\Interim $interim
     *
     * @return DemandeConge
     */
    public function addInterim(\AppBundle\Entity\Interim $interim)
    {
        $this->interim[] = $interim;

        return $this;
    }

    /**
     * Remove interim
     *
     * @param \AppBundle\Entity\Interim $interim
     */
    public function removeInterim(\AppBundle\Entity\Interim $interim)
    {
        $this->interim->removeElement($interim);
    }

    /**
     * Get interim
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInterim()
    {
        return $this->interim;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return DemandeConge
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Add document
     *
     * @param \AppBundle\Entity\Document $document
     *
     * @return DemandeConge
     */
    public function addDocument(\AppBundle\Entity\Document $document)
    {
        $this->document[] = $document;

        return $this;
    }

    /**
     * Remove document
     *
     * @param \AppBundle\Entity\Document $document
     */
    public function removeDocument(\AppBundle\Entity\Document $document)
    {
        $this->document->removeElement($document);
    }

    /**
     * Get document
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Vérifier si le nombre de jour de la demande est valide
     * @param \Symfony\Component\Validator\ExecutionContextInterface $context
     */
    public function checkTotalDays(ExecutionContextInterface $context)
    {
        if ($this->getDemandor()->getSoldePrevisionnel() < $this->totalDays && $this->getTypeConge()->isImputedSolde()) {
            $context->addViolationAt('totalDays', 'Solde prévisionnel insuffisant', array(), null);
        }
        if ($this->totalDays == '' || $this->totalDays <= 0) {
            $context->addViolationAt('totalDays', 'Veuillez vérifir les informations sur les dates de votre demande', array(), null);
        }
        $iCongeValeur = $this->getTypeConge()->getValue();
        //si le nombre de jour pour le type de demande est défini
        if ($iCongeValeur > 0 && $this->totalDays > $iCongeValeur) {
            $context->addViolationAt('totalDays', 'Vous avez droit à ' . $this->getTypeConge()->getValue() . ' jours pour ce type de demande ', array(), null);
        }
    }

}
