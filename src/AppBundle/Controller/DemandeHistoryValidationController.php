<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\DemandeHistoryValidation,
    AppBundle\Form\DemandeHistoryValidationType,
    AppBundle\Form\Search\DemandeHistoryValidationSearch,
    AppBundle\Form\DemandeHistoryValidationSearchType,
    AppBundle\Constants\GestionCongeConstant;

/**
 * DemandeHistoryValidation controller.
 *
 * @Route("/demande-conge/validation")
 */
class DemandeHistoryValidationController extends Controller
{

    /**
     * Lists all DemandeHistoryValidation entities.
     *
     * @Route("/liste", name="demandehistoryvalidation_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cs = $this->get('common.service');
        //init extraParams
        $currentUser = $this->getUser();
        $extraParams = new \stdClass();
        $extraParams->currentUser = $currentUser->getId();
        $validationDemandeSearch = new DemandeHistoryValidationSearch();
		
        //get list id subordonnee for current user
        $subordonneesIds = $cs->getSubordonnees($currentUser);
        $extraParams->isAdmin = $this->isGranted('ROLE_ADMIN');
        //set params request subordonneesIds
        $extraParams->subordonneesIds = implode(',', $subordonneesIds);
        //create form search validation demande
        $form = $this->createForm(new DemandeHistoryValidationSearchType(), $validationDemandeSearch);
        $form->bind($request);
        $demandeHistoryValidations = $em->getRepository('AppBundle:DemandeHistoryValidation')->listeDemandeByCriteria($validationDemandeSearch, $extraParams, true);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $demandeHistoryValidations, $request->query->getInt('page', 1)/* page number */, 10/* limit per page */
        );
        return $this->render('demandehistoryvalidation/index.html.twig', array(
                'pagination' => $pagination,
                'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a DemandeHistoryValidation entity.
     *
     * @Route("/{id}/detail", name="demandehistoryvalidation_show")
     * @Method("GET")
     */
    public function showAction(DemandeHistoryValidation $demandeHistoryValidation)
    {

        return $this->render('demandehistoryvalidation/show.html.twig', array(
                'demandeHistoryValidation' => $demandeHistoryValidation,
        ));
    }

    /**
     * Displays a form to edit an existing DemandeHistoryValidation entity.
     *
     * @Route("/{id}/edit", name="demandehistoryvalidation_edit")
     * @ParamConverter("demandeHistoryValidation", options={"mapping": {"id":"id"}})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, DemandeHistoryValidation $demandeHistoryValidation)
    {
        $editForm = $this->createForm(new DemandeHistoryValidationType(), $demandeHistoryValidation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //check if demande is refused
            if ($demandeHistoryValidation->getValidationStatus() == GestionCongeConstant::CONGE_STATUS_REFUSED) {
                $this->setSoldePrevisionnelOnRefusedDemandeConge($demandeHistoryValidation, $em);
            }
            //add next step workflow in history validation if status is not refused
            if ($demandeHistoryValidation->getValidationStatus() != GestionCongeConstant::CONGE_STATUS_REFUSED) {
                $this->createNextStep($demandeHistoryValidation, $em);
            }
            $em->persist($demandeHistoryValidation);
            $em->flush();
            $this->addFlash('success', 'Cette étape a été bien validé');

            return $this->redirectToRoute('demandehistoryvalidation_index');
        }

        $tValidatorStep = $this->getListeValidatorStep($demandeHistoryValidation->getModeleWorkflowStep());

        return $this->render('demandehistoryvalidation/edit.html.twig', array(
                'demandeHistoryValidation' => $demandeHistoryValidation,
                'validatorsStep' => $tValidatorStep,
                'form' => $editForm->createView(),
        ));
    }

    /**
     * liste de tous les validateurs pour un etape donné
     * @param type $stepId
     *
     * @return type
     */
    private function getListeValidatorStep($stepId)
    {
        $em = $this->getDoctrine()->getManager();
        $tValidators = $em->getRepository('AppBundle:ModeleWorkflowStepsValidator')->findBy(array('modeleWorkflowStepsId' => $stepId));

        return $tValidators;
    }

    /**
     * remettre le solde previsionnel si type congé imputé
     * @param DemandeHistoryValidation $demandeHistoryValidation
     * @param type $em
     */
    private function setSoldePrevisionnelOnRefusedDemandeConge(DemandeHistoryValidation $demandeHistoryValidation, $em)
    {
        $oDemandeConge = $demandeHistoryValidation->getDemandeConge();
        $oDemandeur = $oDemandeConge->getDemandor();
        $bImputedSolde = $oDemandeConge->getTypeConge()->isImputedSolde();
        //check if demande is already refused
        if ($oDemandeConge->getDemandeStatus() == GestionCongeConstant::CONGE_STATUS_REFUSED) {
            $this->addFlash('error', 'Cette demande a été déjà refusée');

            return $this->redirectToRoute('demandehistoryvalidation_index');
        }
        //set solde prev = solde prev + nbr jour demande si type conge imputé
        if ($bImputedSolde) {
            $soldePrev = $oDemandeur->getSoldePrevisionnel() + $oDemandeConge->getTotalDays();
            $oDemandeur->setSoldePrevisionnel($soldePrev);
            //si la demande a été déjà validée : solde reel = solde reel + nbr jour de la demande
            $oDemandeConge->getDemandeStatus() == GestionCongeConstant::CONGE_STATUS_VALIDATED ? $oDemandeur->setSoldeReel($oDemandeur->getSoldeReel() + $oDemandeConge->getTotalDays()) : '';
            $em->persist($oDemandeur);
        }
        //demande de congé directement refusée
        $oDemandeConge->setDemandeStatus($demandeHistoryValidation->getValidationStatus());
        $em->persist($oDemandeConge);
    }

    /**
     * create new history validation for the next step
     * @param DemandeHistoryValidation $demandeHistoryValidation
     * @param type $em
     */
    private function createNextStep(DemandeHistoryValidation $demandeHistoryValidation, $em)
    {
        $oDemandeConge = $demandeHistoryValidation->getDemandeConge();
        $oModelWorkflow = $demandeHistoryValidation->getModeleWorkflowStep()->getModeleWorkflow();
        $iCurrentOrderStep = (int) $demandeHistoryValidation->getModeleWorkflowStep()->getStepOrder();
        //get next step of workflow
        $oNextModeleWorkflowStep = $em->getRepository('AppBundle:ModeleWorkflowSteps')->findOneBy(array('modeleWorkflow' => $oModelWorkflow->getId(), 'stepOrder' => $iCurrentOrderStep + 1));
        //create new history validation if next step exists again
        if ($oNextModeleWorkflowStep) {
            $oNewDemandeHistoryValidation = new DemandeHistoryValidation();
            $oNewDemandeHistoryValidation->setModeleWorkflowStep($oNextModeleWorkflowStep);
            $oNewDemandeHistoryValidation->setDemandeConge($demandeHistoryValidation->getDemandeConge());
            $em->persist($oNewDemandeHistoryValidation);
        } else {
            //check if demande is already validated
            if ($oDemandeConge->getDemandeStatus() == GestionCongeConstant::CONGE_STATUS_VALIDATED) {
                $this->addFlash('error', 'Cette demande a été déjà validée');

                return $this->redirectToRoute('demandehistoryvalidation_index');
            }
            //next step workflow doesn't exist, ie end step validation : validate demande conge
            $oDemandeConge->setDemandeStatus(GestionCongeConstant::CONGE_STATUS_VALIDATED);
            //decrementer le solde réel si type demande imputé
            $oDemandor = $oDemandeConge->getDemandor();
            $iNbrJour = $oDemandeConge->getTotalDays();
            $bImputedSolde = $oDemandeConge->getTypeConge()->isImputedSolde();
            if ($bImputedSolde) {
                $oDemandor->setSoldeReel($oDemandor->getSoldeReel() - $iNbrJour);
                $em->persist($oDemandor);
            }

            $em->persist($oDemandeConge);
        }
    }

    /**
     * bulk validation demande
     * @Route("/bulk-validation", name="bulk_validation_demande")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function searchMasseValidationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $demandeHistoryValidationSearch = new DemandeHistoryValidationSearch();
        $form = $this->createForm(new DemandeHistoryValidationSearchType(), $demandeHistoryValidationSearch);
        $form->bind($request);
        $demandeHistoryValidations = $em->getRepository('AppBundle:DemandeHistoryValidation')->listeDemandeWaintingValidation($demandeHistoryValidationSearch);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $demandeHistoryValidations, $request->query->getInt('page', 1), 10);

        return $this->render('demandehistoryvalidation/masse_validation.html.twig', array(
                'pagination' => $pagination,
                'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/validate-bulk-validation", name="validate_bulk_validation_demande")
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     *
     * @return type
     */
    public function validateAllDemandeAction(Request $request)
    {
        $iValidatedDemande = 0;
        $bReturnQuery = false;
        $demandeHistoryValidationSearch = new DemandeHistoryValidationSearch();
        $form = $this->createForm(new DemandeHistoryValidationSearchType(), $demandeHistoryValidationSearch);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();
            $demandeHistoryValidations = $em->getRepository('AppBundle:DemandeHistoryValidation')->listeDemandeWaintingValidation($demandeHistoryValidationSearch, $bReturnQuery);
            if (count($demandeHistoryValidations)) {
                $iValidatedDemande = $this->validateDemandeList($demandeHistoryValidations, $em);
            }
        }
        $this->addFlash('success', $iValidatedDemande . ' demande(s) validée(s)');

        return $this->redirectToRoute('bulk_validation_demande');
    }

    /**
     * validation des demandes en cours de validation
     * @param type $tDemandeHistoryValidations
     * @param type $em
     *
     * @return int
     */
    private function validateDemandeList($tDemandeHistoryValidations, $em)
    {
        $iValidatedDemande = 0;
        foreach ($tDemandeHistoryValidations as $historyValidation) {
            $oDemandeConge = $historyValidation->getDemandeConge();
            $oDemandor = $oDemandeConge->getDemandor();
            $bImputedSolde = $oDemandeConge->getTypeConge()->isImputedSolde();
            if ($bImputedSolde) {
                $oDemandor->setSoldeReel($oDemandor->getSoldeReel() - $oDemandeConge->getTotalDays());
                $em->persist($oDemandor);
            }
            $historyValidation->setValidationStatus(GestionCongeConstant::CONGE_STATUS_VALIDATED);
            $historyValidation->setCommentaire('Cette demande a été validée via la validation en masse');
            $oDemandeConge->setDemandeStatus(GestionCongeConstant::CONGE_STATUS_VALIDATED);
            $em->persist($oDemandeConge);
            $em->persist($historyValidation);
            $em->flush();
            $iValidatedDemande++;
        }

        return $iValidatedDemande;
    }

}
