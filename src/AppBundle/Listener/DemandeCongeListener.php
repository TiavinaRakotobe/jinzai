<?php

namespace AppBundle\Listener;

use Symfony\Component\DependencyInjection\Container,
    Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\DemandeConge,
    AppBundle\Entity\JoursFeries;

/**
 * DemandeCongeListner
 */
class DemandeCongeListener
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
        }

        //check if entity is instance of DemandeConge
        if ($entity instanceof DemandeConge) {
            $em = $this->container->get('doctrine');
            $zDirectionSigle = $entity->getDemandor()->getDirection()->getSigle();
            //contact current year with direction sigle
            $yearAndDirectionSigle = date('y') . '-' . $zDirectionSigle;
            $nextNumber = $em->getRepository('AppBundle:DemandeConge')->getNextIncrementNumber($yearAndDirectionSigle);
            $entity->setIncrementNumber($nextNumber);
            $congeNumero = str_pad($nextNumber, 5, "0", STR_PAD_LEFT);
            $congeNumero = $yearAndDirectionSigle . '-' . $congeNumero;
            $entity->setNumero($congeNumero);
		
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
