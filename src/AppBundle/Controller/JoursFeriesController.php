<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\JoursFeries;
use AppBundle\Form\JoursFeriesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * JoursFeries controller.
 *
 * @Route("/joursferies")
 * @Security("has_role('ROLE_ADMIN')")
 */
class JoursFeriesController extends Controller
{

    /**
     * Lists all JoursFeries entities.
     *
     * @Route("/", name="joursferies_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $joursFeries = $em->getRepository('AppBundle:JoursFeries')->findAll();

        return $this->render('joursferies/index.html.twig', array(
                'joursFeries' => $joursFeries,
        ));
    }

    /**
     * Creates a new JoursFeries entity.
     *
     * @Route("/editer/{id}", name="joursferies_edit", defaults={"id"= null})
     * @ParamConverter("joursferies", options={"mapping": {"id":"id"}})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, JoursFeries $joursferies = null)
    {
        if (is_null($joursferies)) {
            $joursferies = new JoursFeries();
        }
        $form = $this->createForm(new JoursFeriesType(), $joursferies);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($joursferies);
            $em->flush();
			
			 //$cs = $this->container->get('common.service');
            //incrementer le solde reel et previsionnel si une demande à cette date a été déjà validée
            //$cs->adjustSoldeOnCreateJoursFeries($joursferies->getJoursFeriesDate());

            return $this->redirectToRoute('joursferies_index');
            //return $this->redirectToRoute('joursferies_show', array('id' => $joursferies->getId()));
        }

        return $this->render('joursferies/new.html.twig', array(
                'joursFeries' => $joursferies,
                'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a JoursFeries entity.
     *
     * @Route("/{id}", name="joursferies_show")
     * @Method("GET")
     */
    public function showAction(JoursFeries $joursferies)
    {
        $deleteForm = $this->createDeleteForm($joursferies);

        return $this->render('joursferies/show.html.twig', array(
                'joursFeries' => $joursferies,
                'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a JoursFeries entity.
     *
     * @Route("/{id}", name="joursferies_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, JoursFeries $joursferies)
    {
        $form = $this->createDeleteForm($joursferies);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($joursferies);
            $em->flush();
        }

        return $this->redirectToRoute('joursferies_index');
    }

    /**
     * Creates a form to delete a JoursFeries entity.
     *
     * @param JoursFeries $joursferies The JoursFeries entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(JoursFeries $joursferies)
    {
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('joursferies_delete', array('id' => $joursferies->getId())))
                ->setMethod('DELETE')
                ->getForm()
        ;
    }

}
