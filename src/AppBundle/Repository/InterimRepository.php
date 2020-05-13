<?php

namespace AppBundle\Repository;

/**
 * InterimRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InterimRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * liste des interim du validateur par rapport � la date de cong� demand� par son subordonn�
     * @return array
     */
    public function getListInterimByValidator($demandeconge)
    {
        $zSql = "SELECT i.interim_id FROM `interim` i, utilisateur u WHERE i.utilisateur_id=u.id AND i.utilisateur_id=" . $demandeconge->getDemandor()->getManager()->getId() . " AND i.enabled=1 AND from_date >='" . $demandeconge->getDateStart()->format('Y-m-d') . "' AND to_date <= '" . $demandeconge->getDateEnd()->format('Y-m-d') . "'";
        $qb = $this->getEntityManager()->getConnection()->prepare($zSql);
        $qb->execute();
        $result = $qb->fetchAll(\PDO::FETCH_COLUMN);


        return $result;
    }

}