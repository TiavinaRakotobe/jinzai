<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Traits;

/**
 * CategoryDemande
 *
 * @ORM\Table(name="categorie_demande")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks();
 */
class CategoryDemande
{
    const CONGE = 1;
    const DEMANDE_RH = 2;

    use Traits\id,
        Traits\libelle,
        Traits\createdAt,
        Traits\createdBy,
        Traits\updatedAt,
        Traits\updatedBy,
        Traits\enabled;
}
