<?php

namespace AppBundle\Form\Search;

/**
 * DemandeCongeSearch
 */
class DemandeCongeSearch
{
    /**
     *
     * @var string
     */
    private $numero;

    /**
     *
     * @var string
     */
    private $matricule;

    /**
     *
     * @var integer
     */
    private $demandor;

    /**
     *
     * @var int
     */
    private $direction;

    /**
     *
     * @var int
     */
    private $societe;

    /**
     *
     * @var int
     */
    private $initiator;

    /**
     *
     * @var int
     */
    private $typeConge;

    /**
     *
     * @var string
     */
    private $dateStart;

    /**
     * @var string
     */
    private $dateEnd;

    /**
     * @var int
     */
    private $demandeStatus;

    public function getNumero()
    {
        return $this->numero;
    }

    public function getMatricule()
    {
        return $this->matricule;
    }

    public function getDemandor()
    {
        return $this->demandor;
    }

    public function getDirection()
    {
        return $this->direction;
    }

    public function getSociete()
    {
        return $this->societe;
    }

    public function getInitiator()
    {
        return $this->initiator;
    }

    public function getTypeConge()
    {
        return $this->typeConge;
    }

    public function getDateStart()
    {
        return $this->dateStart;
    }

    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    public function getDemandeStatus()
    {
        return $this->demandeStatus;
    }

    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    public function setMatricule($matricule)
    {
        $this->matricule = $matricule;
    }

    public function setDemandor($demandor)
    {
        $this->demandor = $demandor;
    }

    public function setDirection($direction)
    {
        $this->direction = $direction;
    }

    public function setSociete($societe)
    {
        $this->societe = $societe;
    }

    public function setInitiator($initiator)
    {
        $this->initiator = $initiator;
    }

    public function setTypeConge($typeConge)
    {
        $this->typeConge = $typeConge;
    }

    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;
    }

    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
    }

    public function setDemandeStatus($demandeStatus)
    {
        $this->demandeStatus = $demandeStatus;
    }

}
