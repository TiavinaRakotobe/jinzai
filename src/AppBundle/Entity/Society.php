<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Traits;

/**
 * Society
 *
 * @ORM\Table(name="society")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SocietyRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Society
{

    use Traits\id,
        Traits\libelle,
        Traits\createdAt,
        Traits\createdBy,
        Traits\updatedAt,
        Traits\updatedBy,
        Traits\enabled;
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=10, unique=true)
     */
    private $code;

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Society
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

}
