<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Traits;

/**
 * InterimFonction
 *
 * @ORM\Table(name="interim")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InterimRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Interim
{

    use Traits\id,
        Traits\fromDate,
        Traits\toDate,
        Traits\enabled,
        Traits\createdAt,
        Traits\createdBy,
        Traits\updatedAt,
        Traits\updatedBy;
    /**
     * @var DemandeConge
     *
     * @ORM\ManyToOne(targetEntity="DemandeConge", inversedBy="interim")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="demande_conge_id", referencedColumnName="id")
     * })
     */
    private $demandeConge;

    /**
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Admin\UserBundle\Entity\Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     * })
     */
    private $utilisateur;

    /**
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Admin\UserBundle\Entity\Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="interim_id", referencedColumnName="id")
     * })
     */
    private $interim;

    /**
     * @var int
     *
     * @ORM\Column(name="interim_type", type="integer")
     */
    private $interimType;

    /**
     *
     */
    public function __construct()
    {
        $this->enabled = true;
    }

    /**
     * Set interimType
     *
     * @param integer $interimType
     *
     * @return Interim
     */
    public function setInterimType($interimType)
    {
        $this->interimType = $interimType;

        return $this;
    }

    /**
     * Get interimType
     *
     * @return integer
     */
    public function getInterimType()
    {
        return $this->interimType;
    }

    /**
     * Set demandeConge
     *
     * @param \AppBundle\Entity\DemandeConge $demandeConge
     *
     * @return Interim
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
     * Set utilisateur
     *
     * @param \Admin\UserBundle\Entity\Utilisateur $utilisateur
     *
     * @return Interim
     */
    public function setUtilisateur(\Admin\UserBundle\Entity\Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \Admin\UserBundle\Entity\Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set interim
     *
     * @param \Admin\UserBundle\Entity\Utilisateur $interim
     *
     * @return Interim
     */
    public function setInterim(\Admin\UserBundle\Entity\Utilisateur $interim = null)
    {
        $this->interim = $interim;

        return $this;
    }

    /**
     * Get interim
     *
     * @return \Admin\UserBundle\Entity\Utilisateur
     */
    public function getInterim()
    {
        return $this->interim;
    }

}
