<?php

namespace AppBundle\Listener;

use Symfony\Component\DependencyInjection\Container,
    Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\DemandeConge,
    AppBundle\Entity\JoursFeries;

/**
 * JoursFeriesListener
 */
class JoursFeriesListener
{
    /**
     *
     * @var Container
     */
    protected $container;

    /**
     * prePersist
     * @param LifecycleEventArgs $args
     * @return type
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        if (method_exists($entity, "setCreatedBy")) {
            $entity->setCreatedBy($currentUser);
			//$entity->setEnabled(true);
        }

        //check if entity is instance of JoursFeries
        if ($entity instanceof JoursFeries) {
            $cs = $this->container->get('common.service');
            //incrementer le solde reel et previsionnel si une demande à cette date a été déjà validée
            $cs->adjustSoldeOnCreateJoursFeries($entity->getJoursFeriesDate());
        }

        return;
    }

    /**
     * preUpdate
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $currentUser = $this->container->get('security.context')->getToken()->getUser();
        if (method_exists($entity, "setUpdatedBy")) {
            $entity->setUpdatedBy($currentUser);
        }
    }

    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

}
