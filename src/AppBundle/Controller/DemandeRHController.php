<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\DemandeRH,
    AppBundle\Form\DemandeRHType,
    AppBundle\Form\Search\DemandeRhSearch,
	AppBundle\Constants\GestionCongeConstant,
    AppBundle\Form\DemandeRhSearchType;

/**
 * DemandeRH controller.
 *
 * @Route("/demande-rh")
 */
class DemandeRHController extends Controller
{

    /**
     * Lists all DemandeRH entities.
     *
     * @Route("/liste-demande", name="demanderh_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $demandeRhSearch = new DemandeRhSearch();
        $extraParams = new \stdClass();
        //create form search demande
        $form = $this->createForm(new DemandeRhSearchType(), $demandeRhSearch);
        $form->bind($request);
        $result = $em->getRepository('AppBundle:DemandeRH')->listeDemandeByCriteria($demandeRhSearch, $extraParams, true);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $result, $request->query->getInt('page', 1), 10);

        return $this->render('demanderh/index.html.twig', array(
                'pagination' => $pagination,
                'form' => $form->createView()
        ));
    }

    /**
     * Creates a new DemandeRH entity.
     *
     * @Route("/create-demande", name="demanderh_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $demandeRH = new DemandeRH();
        $form = $this->createForm(new DemandeRHType(), $demandeRH);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
			$demandeRH->setDemandeRhStatus(GestionCongeConstant::DEMANDE_RH_STATUS_INPROGRESS);
            $em->persist($demandeRH);
            $em->flush();

            return $this->redirectToRoute('demanderh_show', array('id' => $demandeRH->getId()));
        }

        return $this->render('demanderh/new.html.twig', array(
                'demandeRH' => $demandeRH,
                'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a DemandeRH entity.
     *
     * @Route("/detail/{id}", name="demanderh_show")
     * @Method("GET")
     */
    public function showAction(DemandeRH $demandeRH)
    {

        return $this->render('demanderh/show.html.twig', array(
                'demandeRH' => $demandeRH
        ));
    }

    /**
     * Displays a form to edit an existing DemandeRH entity.
     *
     * @Route("/{id}/edit", name="demanderh_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, DemandeRH $demandeRH)
    {
        $editForm = $this->createForm(new DemandeRHType(), $demandeRH);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($demandeRH);
            $em->flush();

            return $this->redirectToRoute('demanderh_edit', array('id' => $demandeRH->getId()));
        }

        return $this->render('demanderh/edit.html.twig', array(
                'demandeRH' => $demandeRH,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing DemandeRH entity.
     *
     * @Route("/{id}/change/{status}", name="demanderh_change_state")
     * @Method({"GET", "POST"})
     */
    public function changeStateAction(DemandeRH $demandeRH, $status)
    {
        if (!$demandeRH) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        }

        $em = $this->getDoctrine()->getManager();
        $demandeRH->setDemandeRhStatus($status);
        $em->persist($demandeRH);
        $em->flush();

        return $this->redirectToRoute('demanderh_index');
    }

}
