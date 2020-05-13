<?php

namespace AppBundle\Form\Search;

/**
 * UtilisateurSearch
 */
class UtilisateurSearch
{
    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var string
     */
    private $matricule;
    
	/**
     *
     * @var string
     */
    private $username;

    /**
     *
     * @var int
     */
    private $direction;
	

    public function getUsername()
    {
        return $this->username;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getMatricule()
    {
        return $this->matricule;
    }

   
    public function getDirection()
    {
        return $this->direction;
    }

   public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setName($name)
    {
        return $this->name;
    }

    public function setMatricule($matricule)
    {
        $this->matricule=$matricule;
    }

   
    public function setDirection($direction)
    {
        $this->direction=$direction;
    }

}
