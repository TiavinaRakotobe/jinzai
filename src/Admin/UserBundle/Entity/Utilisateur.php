<?php

namespace Admin\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;
use AppBundle\Traits;
use Doctrine\ORM\Mapping\AttributeOverrides;
use Doctrine\ORM\Mapping\AttributeOverride;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Utilisateur
 *
 * @ORM\Table(name="utilisateur")
 * @ORM\Entity(repositoryClass="Admin\UserBundle\Repository\UtilisateurRepository")
 * @ORM\HasLifecycleCallbacks()
 * @AttributeOverrides({
 * @AttributeOverride(name="email",
 *         column=@ORM\Column(
 *             name="email",
 *             type="string",
 *             length=255,
 *             nullable=true
 *         )
 *     ),
 *     @AttributeOverride(name="emailCanonical",
 *         column=@ORM\Column(
 *             name="email_canonical",
 *             type="string",
 *             length=255,
 *             nullable=true
 *         )
 *     )
 * })
 * @UniqueEntity("matricule", message="matricule already used")
 * @UniqueEntity("username", message="username already used")
 * @UniqueEntity("email", message="email already used")
 * @ORM\HasLifecycleCallbacks()
 * @Assert\Callback(methods={"validateEmailValue"})
 * @Assert\Callback(methods={"validateUsernameValue"})
 * @Assert\Callback(methods={"validatePasswordValue"})
 */
class Utilisateur extends BaseUser
{
    #use Traits\id;

use Traits\createdAt,
    Traits\createdBy,
    Traits\updatedAt,
    Traits\updatedBy;
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank(
     * message="Champ obligatoire"
     * )
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank(
     * message="Champ obligatoire"
     * )
     * @ORM\Column(name="matricule", type="string", length=255, unique=true)
     */
    private $matricule;

    /**
     * @var float
     *
     * @ORM\Column(name="soldeInitial", type="float", nullable=true)
     */
    private $soldeInitial;

    /**
     * @var float
     *
     * @ORM\Column(name="soldeReel", type="float", nullable=true)
     */
    private $soldeReel;

    /**
     * @var float
     *
     * @ORM\Column(name="soldePrevisionnel", type="float", nullable=true)
     */
    private $soldePrevisionnel;

    /**
     * @var integer
     *
     * @ORM\Column(name="firstConnected", type="float", nullable=true)
     */
    private $firstConnected;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFirstConnected", type="datetime", nullable=true)
     */
    protected $dateFirstConnected;

    /**
     * @var \Date
     *
     * @ORM\Column(name="dateEmbauche", type="date", nullable=true)
     */
    protected $dateEmbauche;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="manager", referencedColumnName="id")
     * })
     */
    private $manager;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=255, nullable=true)
     */
    private $mobile;

    /**
     * @var Direction
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Direction")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="direction_id", referencedColumnName="id")
     * })
     */
    private $direction;

    /**
     * @var string
     *
     * @ORM\Column(name="societe", type="string", length=255, nullable=true)
     */
    private $societe;

    /**
     * @var bool
     *
     * @ORM\Column(name="expat", type="boolean", options={"default"=false})
     */
    private $expat;

    /**
     * @var string
     * @ORM\Column(name="photo", type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        if (empty($this->roles)) {
            $this->roles[] = 'ROLE_USER';
            $this->soldeInitial = 0;
            $this->soldeReel = 0;
            $this->soldePrevisionnel = 0;
        }
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Utilisateur
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set matricule
     *
     * @param string $matricule
     *
     * @return Utilisateur
     */
    public function setMatricule($matricule)
    {
        $this->matricule = $matricule;

        return $this;
    }

    /**
     * Get matricule
     *
     * @return string
     */
    public function getMatricule()
    {
        return $this->matricule;
    }

    /**
     * Set soldeInitial
     *
     * @param float $soldeInitial
     *
     * @return Utilisateur
     */
    public function setSoldeInitial($soldeInitial)
    {
        $this->soldeInitial = $soldeInitial;

        return $this;
    }

    /**
     * Get soldeInitial
     *
     * @return float
     */
    public function getSoldeInitial()
    {
        return $this->soldeInitial;
    }

    /**
     * Set soldeReel
     *
     * @param float $soldeReel
     *
     * @return Utilisateur
     */
    public function setSoldeReel($soldeReel)
    {
        $this->soldeReel = $soldeReel;

        return $this;
    }

    /**
     * Get soldeReel
     *
     * @return float
     */
    public function getSoldeReel()
    {
        return $this->soldeReel;
    }

    /**
     * Set soldePrevisionnel
     *
     * @param float $soldePrevisionnel
     *
     * @return Utilisateur
     */
    public function setSoldePrevisionnel($soldePrevisionnel)
    {
        $this->soldePrevisionnel = $soldePrevisionnel;

        return $this;
    }

    /**
     * Get soldePrevisionnel
     *
     * @return float
     */
    public function getSoldePrevisionnel()
    {
        return $this->soldePrevisionnel;
    }

    /**
     * Set dateEmbauche
     *
     * @param Date $dateEmbauche
     *
     * @return Utilisateur
     */
    public function setDateEmbauche($dateEmbauche)
    {
        $this->dateEmbauche = $dateEmbauche;

        return $this;
    }

    /**
     * Get dateEmbauche
     *
     * @return Date
     */
    public function getDateEmbauche()
    {
        return $this->dateEmbauche;
    }

    /**
     * Set firstConnected
     *
     * @param int $firstConnected
     *
     * @return Utilisateur
     */
    public function setFirstConnected($firstConnected)
    {
        $this->firstConnected = $firstConnected;

        return $this;
    }

    /**
     * Get firstConnected
     *
     * @return int
     */
    public function getFirstConnected()
    {
        return $this->firstConnected;
    }

    /**
     * Set dateFirstConnected
     *
     * @param \DateTime $dateFirstConnected
     *
     * @return Utilisateur
     */
    public function setDateFirstConnected($dateFirstConnected)
    {
        $this->dateFirstConnected = $dateFirstConnected;

        return $this;
    }

    /**
     * Get dateFirstConnected
     *
     * @return \DateTime
     */
    public function getDateFirstConnected()
    {
        return $this->dateFirstConnected;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Utilisateur
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     *
     * @return Utilisateur
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
        return $this;
    }

    /**
     * Get mobile
     *
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set societe
     *
     * @param string $societe
     *
     * @return Utilisateur
     */
    public function setSociete($societe)
    {
        $this->societe = $societe;

        return $this;
    }

    /**
     * Get societe
     *
     * @return string
     */
    public function getSociete()
    {
        return $this->societe;
    }

    /**
     * Set expat
     *
     * @param boolean $expat
     *
     * @return Utilisateur
     */
    public function setExpat($expat)
    {
        $this->expat = $expat;

        return $this;
    }

    /**
     * Get expat
     *
     * @return bool
     */
    public function getExpat()
    {
        return $this->expat;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Utilisateur
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Set manager
     *
     * @param \Admin\UserBundle\Entity\Utilisateur $manager
     *
     * @return Utilisateur
     */
    public function setManager(\Admin\UserBundle\Entity\Utilisateur $manager = null)
    {
        $this->manager = $manager;
        return $this;
    }

    /**
     * Get manager
     *
     * @return \Admin\UserBundle\Entity\Utilisateur
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * email format validation
     * @param ExecutionContextInterface $context
     */
    public function validateEmailValue(ExecutionContextInterface $context)
    {
        if (!empty($this->email)) {
            if (false === filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $context->addViolationAt('email', 'Veuillez entrer un email valide', array(), null);
            }
        }
    }

    public function validateUsernameValue(ExecutionContextInterface $context)
    {
        if (empty($this->username)) {
            $context->addViolationAt('username', 'Veuillez entrer un nom d\'utilisateur', array(), null);
        }
    }

    public function validatePasswordValue(ExecutionContextInterface $context)
    {
        if (empty($this->plainPassword)) {
            $context->addViolationAt('password', 'Veuillez entrer un mot de passe', array(), null);
        }
    }

    /**
     * Set direction
     *
     * @param \AppBundle\Entity\Direction $direction
     *
     * @return Utilisateur
     */
    public function setDirection(\AppBundle\Entity\Direction $direction = null)
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * Get direction
     *
     * @return \AppBundle\Entity\Direction
     */
    public function getDirection()
    {
        return $this->direction;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

}
