<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Direction;
use AppBundle\Form\DirectionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Direction controller.
 *
 * @Route("/direction")
 * @Security("has_role('ROLE_ADMIN')")
 */
class DirectionController extends Controller
{

    /**
     * Lists all Direction entities.
     *
     * @Route("/", name="direction_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $directions = $em->getRepository('AppBundle:Direction')->findAll();

        return $this->render('direction/index.html.twig', array(
                'directions' => $directions,
        ));
    }

    /**
     * Creates or edit a Direction entity.
     *
     * @Route("/editer/{id}", name="direction_edit", defaults={"id"=null})
     * @ParamConverter("direction", options={"mapping": {"id":"id"}})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, Direction $direction = null)
    {
        if (is_null($direction)) {
            $direction = new Direction();
        }
        $form = $this->createForm(new DirectionType(), $direction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($direction);
            $em->flush();
            $this->addFlash('success', 'Direction crée avec succès');

            return $this->redirectToRoute('direction_index');
        }

        return $this->render('direction/new.html.twig', array(
                'direction' => $direction,
                'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Direction entity.
     *
     * @Route("/{id}", name="direction_show")
     * @Method("GET")
     */
    public function showAction(Direction $direction)
    {
        $deleteForm = $this->createDeleteForm($direction);

        return $this->render('direction/show.html.twig', array(
                'direction' => $direction,
                'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Direction entity.
     *
     * @Route("/{id}", name="direction_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Direction $direction)
    {
        $form = $this->createDeleteForm($direction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($direction);
            $em->flush();
        }

        return $this->redirectToRoute('direction_index');
    }

    /**
     * Creates a form to delete a Direction entity.
     *
     * @param Direction $direction The Direction entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Direction $direction)
    {
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('direction_delete', array('id' => $direction->getId())))
                ->setMethod('DELETE')
                ->getForm()
        ;
    }

}
