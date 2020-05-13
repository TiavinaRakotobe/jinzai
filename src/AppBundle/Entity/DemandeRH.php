<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Traits;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DemandeRH
 *
 * @ORM\Table(name="demande_rh")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DemandeRHRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class DemandeRH
{

    use Traits\id,
        Traits\createdAt,
        Traits\createdBy,
        Traits\updatedAt,
        Traits\updatedBy,
        Traits\fromDate;
        //Traits\toDate;
    /**
     * @var TypeConge
     *
     * @ORM\ManyToOne(targetEntity="TypeConge")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_demande_id", referencedColumnName="id")
     * })
     *
     * @Assert\NotBlank(message="Champ obligatoire")
     */
    private $typeDemande;

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
     * @var int
     *
     * @ORM\Column(name="demande_rh_nombre", type="integer", options={"default":0})
	 * @Assert\NotBlank(message="Champ obligatoire")
     */
    private $nombreDemande;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="text", nullable=true)
     */
    private $commentaire;

    /**
     * @var int
     *
     * @ORM\Column(name="demande_rh_status", type="integer", options={"default":0})
     */
    private $demandeRhStatus;

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return DemandeRH
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
     * Set typeDemande
     *
     * @param \AppBundle\Entity\TypeConge $typeDemande
     *
     * @return DemandeRH
     */
    public function setTypeDemande(\AppBundle\Entity\TypeConge $typeDemande = null)
    {
        $this->typeDemande = $typeDemande;

        return $this;
    }

    /**
     * Get typeDemande
     *
     * @return \AppBundle\Entity\TypeConge
     */
    public function getTypeDemande()
    {
        return $this->typeDemande;
    }

    /**
     * Set demandor
     *
     * @param \Admin\UserBundle\Entity\Utilisateur $demandor
     *
     * @return DemandeRH
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

    public function getDemandeRhStatus()
    {
        return $this->demandeRhStatus;
    }

    public function setDemandeRhStatus($demandeRhStatus)
    {
        $this->demandeRhStatus = $demandeRhStatus;
    }
	
	/**
     * @return int
     */
    public function getNombreDemande()
    {
        return $this->nombreDemande;
    }

    /**
     * @param int $nombreDemande
     */
    public function setNombreDemande($nombreDemande)
    {
        $this->nombreDemande = $nombreDemande;
    }

}
