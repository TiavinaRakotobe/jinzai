<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\TypeConge;
use AppBundle\Form\TypeCongeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * TypeConge controller.
 *
 * @Route("/type-conge")
 * @Security("has_role('ROLE_ADMIN')")
 */
class TypeCongeController extends Controller
{

    /**
     * Lists all TypeConge entities.
     *
     * @Route("/", name="typeconge_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $typeConges = $em->getRepository('AppBundle:TypeConge')->findAll();

        return $this->render('typeconge/index.html.twig', array(
                'typeConges' => $typeConges,
        ));
    }

    /**
     * Creates a new TypeConge entity.
     *
     * @Route("/editer/{id}", name="typeconge_edit", defaults={"id"= null})
     * @ParamConverter("typeConge", options={"mapping": {"id":"id"}})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, TypeConge $typeConge = null)
    {
        if (is_null($typeConge)) {
            $typeConge = new TypeConge();
        }
        $form = $this->createForm(new TypeCongeType(), $typeConge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($typeConge);
            $em->flush();
            $this->addFlash('success', 'Le type de congé ' . $typeConge->getLibelle() . ' a été bien sauvegardé');

            return $this->redirectToRoute('typeconge_index');
        }

        return $this->render('typeconge/new.html.twig', array(
                'typeConge' => $typeConge,
                'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a TypeConge entity.
     *
     * @Route("/{id}", name="typeconge_show")
     * @Method("GET")
     */
    public function showAction(TypeConge $typeConge)
    {
        $deleteForm = $this->createDeleteForm($typeConge);

        return $this->render('typeconge/show.html.twig', array(
                'typeConge' => $typeConge,
                'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a TypeConge entity.
     *
     * @Route("/{id}", name="typeconge_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, TypeConge $typeConge)
    {
        $form = $this->createDeleteForm($typeConge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($typeConge);
            $em->flush();
        }

        return $this->redirectToRoute('typeconge_index');
    }

    /**
     * Creates a form to delete a TypeConge entity.
     *
     * @param TypeConge $typeConge The TypeConge entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(TypeConge $typeConge)
    {
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('typeconge_delete', array('id' => $typeConge->getId())))
                ->setMethod('DELETE')
                ->getForm()
        ;
    }

}
