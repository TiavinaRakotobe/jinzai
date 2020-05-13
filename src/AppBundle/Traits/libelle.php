<?php

namespace AppBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class libelle
 * @package AppBundle\Traits
 */
trait libelle
{
    /**
     * @Assert\NotBlank(message="Champ obligatoire")
     * @ORM\Column(name="libelle", type="string", nullable=true)
     */
    protected $libelle;

    /**
     * @return mixed
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * @param mixed $libelle
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    }

}
