<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\ModeleWorkflowStepsValidator;
use AppBundle\Form\ModeleWorkflowStepsValidatorType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * ModeleWorkflowStepsValidator controller.
 *
 * @Route("/modeleworkflow-steps-validator")
 */
class ModeleWorkflowStepsValidatorController extends Controller
{

    /**
     * Lists all ModeleWorkflowStepsValidator entities.
     *
     * @Route("/", name="modeleworkflowstepsvalidator_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $modeleWorkflowStepsValidators = $em->getRepository('AppBundle:ModeleWorkflowStepsValidator')->findAll();

        return $this->render('modeleworkflowstepsvalidator/index.html.twig', array(
                'modeleWorkflowStepsValidators' => $modeleWorkflowStepsValidators,
        ));
    }

    /**
     * Creates a new ModeleWorkflowStepsValidator entity.
     *
     * @Route("/new", name="modeleworkflowstepsvalidator_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $modeleWorkflowStepsValidator = new ModeleWorkflowStepsValidator();
        $form = $this->createForm(new ModeleWorkflowStepsValidatorType(), $modeleWorkflowStepsValidator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($modeleWorkflowStepsValidator);
            $em->flush();

            return $this->redirectToRoute('modeleworkflowstepsvalidator_show', array('id' => $modeleWorkflowStepsValidator->getId()));
        }

        return $this->render('modeleworkflowstepsvalidator/new.html.twig', array(
                'modeleWorkflowStepsValidator' => $modeleWorkflowStepsValidator,
                'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ModeleWorkflowStepsValidator entity.
     *
     * @Route("/{id}", name="modeleworkflowstepsvalidator_show")
     * @Method("GET")
     */
    public function showAction(ModeleWorkflowStepsValidator $modeleWorkflowStepsValidator)
    {
        $deleteForm = $this->createDeleteForm($modeleWorkflowStepsValidator);

        return $this->render('modeleworkflowstepsvalidator/show.html.twig', array(
                'modeleWorkflowStepsValidator' => $modeleWorkflowStepsValidator,
                'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ModeleWorkflowStepsValidator entity.
     *
     * @Route("/{id}/edit", name="modeleworkflowstepsvalidator_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, ModeleWorkflowStepsValidator $modeleWorkflowStepsValidator)
    {
        $deleteForm = $this->createDeleteForm($modeleWorkflowStepsValidator);
        $editForm = $this->createForm(new ModeleWorkflowStepsValidatorType(), $modeleWorkflowStepsValidator);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($modeleWorkflowStepsValidator);
            $em->flush();

            return $this->redirectToRoute('modeleworkflowstepsvalidator_edit', array('id' => $modeleWorkflowStepsValidator->getId()));
        }

        return $this->render('modeleworkflowstepsvalidator/edit.html.twig', array(
                'modeleWorkflowStepsValidator' => $modeleWorkflowStepsValidator,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ModeleWorkflowStepsValidator entity.
     *
     * @Route("/{id}", name="modeleworkflowstepsvalidator_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, ModeleWorkflowStepsValidator $modeleWorkflowStepsValidator)
    {
        $form = $this->createDeleteForm($modeleWorkflowStepsValidator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($modeleWorkflowStepsValidator);
            $em->flush();
        }

        return $this->redirectToRoute('modeleworkflowstepsvalidator_index');
    }

    /**
     * Creates a form to delete a ModeleWorkflowStepsValidator entity.
     *
     * @param ModeleWorkflowStepsValidator $modeleWorkflowStepsValidator The ModeleWorkflowStepsValidator entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ModeleWorkflowStepsValidator $modeleWorkflowStepsValidator)
    {
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('modeleworkflowstepsvalidator_delete', array('id' => $modeleWorkflowStepsValidator->getId())))
                ->setMethod('DELETE')
                ->getForm()
        ;
    }

    /**
     * Deletes a ModeleWorkflowStepsValidator entity via ajax.
     *
     * @Route("/ajax-remove-step-validator", name="ajax_modeleworkflowstepsvalidator_delete")
     *
     * @return JsonResponse
     */
    public function ajaxDeleteValidatorAction(Request $request)
    {
        $id = $request->request->get('id');
        $data = array('status' => 0, 'code' => 200);
        $response = new JsonResponse();
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $oModeleWorkflowStepsValidators = $em->getRepository('AppBundle:ModeleWorkflowStepsValidator')->find($id);
            if ($oModeleWorkflowStepsValidators) {
                $em->remove($oModeleWorkflowStepsValidators);
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

    /**
     * ajaxAddValidatorStepAction : ajax add validator step
     * @param Request $request
     * @Route("/ajax-add-validator-step", name="ajax_modeleworkflowstepsvalidator_add")
     *
     * @return JsonResponse
     */
    public function ajaxAddValidatorStepAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = array('code' => 0, 'exist' => false);
        $response = new JsonResponse();
        //retrieve posted parameters from ajax
        $stepId = $request->request->get('step_id');
        $validatorId = $request->request->get('validator_id');
        if ($request->isXmlHttpRequest()) {
            //check if validator already exists
            $bExist = $em->getRepository('AppBundle:ModeleWorkflowStepsValidator')->findOneBy(array(
                'modeleWorkflowStepsId' => $stepId,
                'validator' => $validatorId
            ));
            if ($bExist) {
                //don't save if validator already exists
                $data['exist'] = true;
                $response->setData($data);

                return $response;
            }

            $oModeleWorkflowStep = $em->getRepository('AppBundle:ModeleWorkflowSteps')->find($stepId);
            $oUtilisateur = $em->getRepository('AdminUserBundle:Utilisateur')->find($validatorId);

            //create new step validator
            $oModeleWorkflowStepsValidators = new ModeleWorkflowStepsValidator();
            $oModeleWorkflowStepsValidators->setValidator($oUtilisateur);
            $oModeleWorkflowStepsValidators->setModeleWorkflowStepsId($oModeleWorkflowStep);
            $em->persist($oModeleWorkflowStepsValidators);
            $em->flush();
            $data['code'] = 200;
            $data['id'] = $oModeleWorkflowStepsValidators->getId();
            $data['validatorId'] = $oModeleWorkflowStepsValidators->getValidator()->getId();
            $data['name'] = $oModeleWorkflowStepsValidators->getValidator()->getName();
            $response->setData($data);

            return $response;
        }

        return;
    }

}
