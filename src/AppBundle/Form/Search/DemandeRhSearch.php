<?php

namespace AppBundle\Form\Search;

/**
 * DemandeRhSearch
 */
class DemandeRhSearch
{
    /**
     *
     * @var integer
     */
    private $demandor;

    /**
     *
     * @var int
     */
    private $typeDemande;

    /**
     *
     * @var string
     */
    private $fromDate;

    /**
     * @var string
     */
    private $toDate;

    /**
     * @var int
     */
    private $demandeStatus;

    public function getDemandor()
    {
        return $this->demandor;
    }

    public function getTypeDemande()
    {
        return $this->typeDemande;
    }

    public function getFromDate()
    {
        return $this->fromDate;
    }

    public function getToDate()
    {
        return $this->toDate;
    }

    public function getDemandeStatus()
    {
        return $this->demandeStatus;
    }

    public function setDemandor($demandor)
    {
        $this->demandor = $demandor;
    }

    public function setTypeDemande($typeDemande)
    {
        $this->typeDemande = $typeDemande;
    }

    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;
    }

    public function setToDate($toDate)
    {
        $this->toDate = $toDate;
    }

    public function setDemandeStatus($demandeStatus)
    {
        $this->demandeStatus = $demandeStatus;
    }

}
