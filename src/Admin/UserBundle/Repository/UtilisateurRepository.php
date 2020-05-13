<?php

namespace Admin\UserBundle\Repository;

use Admin\UserBundle\Entity\Utilisateur,
	AppBundle\Form\Search\UtilisateurSearch;

/**
 * UtilisateurRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UtilisateurRepository extends \Doctrine\ORM\EntityRepository
{

    public function listeSubordonnee(Utilisateur $user = null)
    {
        $queryBuilder = $this->createQueryBuilder('U')
            ->where('U.enabled = 1');
        if (!is_null($user)) {
            $queryBuilder->andWhere('U.manager = :userId')
                ->setParameter('userId', $user->getId());
        }
        $query = $queryBuilder->getQuery();
        $result = $query->getResult();

        return $result;
    }
	
	/*
	* get user information by Username
	*/
	public function getUserByUsername($username)
    {
        $queryBuilder = $this->createQueryBuilder('U')
            ->where('U.enabled = 1');
        if (!is_null($username)) {
            $queryBuilder->andWhere('U.username = :username')
                ->setParameter('username', $username);
        }
        $query = $queryBuilder->getQuery();
        $result = $query->getResult();

        return $result;
    }

    /**
     * list user by criteria
     * @param type $criteria
     *
     * @return type
     */
    public function listeUserByCriteria(UtilisateurSearch $utilisateurSearch = null, $extraParams = null, $isQueryToReturn = true)
    {
        $clauseWhere = '';
        $params = array();
        $qb = $this->createQueryBuilder('U');
        $qb->add('select', 'U');
        $qb->add('from', 'AdminUserBundle:Utilisateur U');
        $qb->leftJoin('AppBundle:Direction', 'D', 'WITH', 'U.direction = D.id');
        $qb->where(' 1=1 ');

		//check name
        if ($utilisateurSearch && $utilisateurSearch->getName() != '') {
            $clauseWhere .= ($clauseWhere != '') ? " AND " : "";
            $clauseWhere .= " U.name LIKE :name ";
            $params['name'] = '%' . trim($utilisateurSearch->getName()) . '%';
        }
		
		//check matricule
        if ($utilisateurSearch && $utilisateurSearch->getMatricule() != '') {
            $clauseWhere .= ($clauseWhere != '') ? " AND " : "";
            $clauseWhere .= " U.matricule = :matricule ";
            $params['matricule'] = trim($utilisateurSearch->getMatricule());
        }
		
		//check username
        if ($utilisateurSearch && $utilisateurSearch->getUsername() != '') {
            $clauseWhere .= ($clauseWhere != '') ? " AND " : "";
            $clauseWhere .= " U.username = :username ";
            $params['username'] = trim($utilisateurSearch->getUsername());
        }
		
		//check absence
        if ($extraParams != null && isset($extraParams->absence) && $extraParams->absence != null) {
            $clauseWhere .= ($clauseWhere != '') ? " AND " : "";
            $clauseWhere .= " U.username = :username ";
            $params['username'] = trim($extraParams->absence);
        }
		
		 //check direction
        if ($utilisateurSearch && $utilisateurSearch->getDirection() != '') {
            $clauseWhere .= ($clauseWhere != '') ? " AND " : "";
            $clauseWhere .= " D.id = :direction ";
            $params['direction'] = $utilisateurSearch->getDirection();
        }
		
        if ($clauseWhere != '') {
            $qb->add('where', $clauseWhere);
        }

        if (count($params) > 0) {
            //set all query parameters
            foreach ($params as $field => $values) {
                $qb->setParameter($field, $values);
            }
        }

        $query = $qb->getQuery();

        return $query->getResult();
		//return $isQueryToReturn ? $query : $query->getResult();
    }

}