<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\ModeleWorkflow;
use AppBundle\Form\ModeleWorkflowType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * ModeleWorkflow controller.
 *
 * @Route("/modele-workflow")
 * @Security("has_role('ROLE_ADMIN')")
 */
class ModeleWorkflowController extends Controller
{

    /**
     * Lists all ModeleWorkflow entities.
     *
     * @Route("/", name="modeleworkflow_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $modeleWorkflows = $em->getRepository('AppBundle:ModeleWorkflow')->findAll();

        return $this->render('modeleworkflow/index.html.twig', array(
                'modeleWorkflows' => $modeleWorkflows,
        ));
    }

    /**
     * Creates a new ModeleWorkflow entity.
     *
     * @Route("/editer/{id}", name="modeleworkflow_edit", defaults={"id"= null})
     * @ParamConverter("modeleWorkflow", options={"mapping": {"id":"id"}})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, ModeleWorkflow $modeleWorkflow = null)
    {
        if (is_null($modeleWorkflow)) {
            $modeleWorkflow = new ModeleWorkflow();
        }
        $form = $this->createForm(new ModeleWorkflowType(), $modeleWorkflow);

        //remove field modeleWorkflowSteps if edit object
        if ($modeleWorkflow->getId()) {
            //$form->remove('modeleWorkflowSteps');
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($modeleWorkflow);
            //check if step is set
            $tModeleWorkflowSteps = $modeleWorkflow->getModeleWorkflowSteps();
            if (count($tModeleWorkflowSteps)) {
                foreach ($tModeleWorkflowSteps as $steps) {
                    if ($steps->getModeleWorkflow() == null) {
                        //take current $modeleWorkflow as modeleWorkflow for the step
                        $steps->setModeleWorkflow($modeleWorkflow);
                        $em->persist($steps);
                    }
                }
            }
            $em->flush();
            $this->addFlash('success', 'Cet élément a été bien enregistré');

            return $this->redirectToRoute('modeleworkflow_show', array('id' => $modeleWorkflow->getId()));
        }

        return $this->render('modeleworkflow/new.html.twig', array(
                'modeleWorkflow' => $modeleWorkflow,
                'form' => $form->createView(),
                'modeleWorkflowSteps' => $modeleWorkflow->getModeleWorkflowSteps(),
        ));
    }

    /**
     * Finds and displays a ModeleWorkflow entity.
     *
     * @Route("/{id}", name="modeleworkflow_show")
     * @Method("GET")
     */
    public function showAction(ModeleWorkflow $modeleWorkflow)
    {
        $deleteForm = $this->createDeleteForm($modeleWorkflow);

        return $this->render('modeleworkflow/show.html.twig', array(
                'modeleWorkflow' => $modeleWorkflow,
                'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ModeleWorkflow entity.
     *
     * @Route("/{id}", name="modeleworkflow_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, ModeleWorkflow $modeleWorkflow)
    {
        $form = $this->createDeleteForm($modeleWorkflow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($modeleWorkflow);
            $em->flush();
        }

        return $this->redirectToRoute('modeleworkflow_index');
    }

    /**
     * Creates a form to delete a ModeleWorkflow entity.
     *
     * @param ModeleWorkflow $modeleWorkflow The ModeleWorkflow entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ModeleWorkflow $modeleWorkflow)
    {
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('modeleworkflow_delete', array('id' => $modeleWorkflow->getId())))
                ->setMethod('DELETE')
                ->getForm()
        ;
    }

    /**
     * Deletes a ModeleWorkflow entity.via ajax
     *
     * @Route("/ajax-delete-step-wf", name="ajax_modeleworkflow_delete_step")
     */
    public function ajaxDeleteAction(Request $request)
    {
        $id = $request->request->get('id');
        $data = array('status' => 0, 'code' => 200);
        $response = new JsonResponse();
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $oModeleWorkflowSteps = $em->getRepository('AppBundle:ModeleWorkflowSteps')->find($id);
            if ($oModeleWorkflowSteps) {
                $em->remove($oModeleWorkflowSteps);
                $em->flush();
                $data['status'] = 1;
            } else {
                $data['code'] = 404;
            }

            $response->setData($data);

            return $response;
        }

        return;
    }

}
