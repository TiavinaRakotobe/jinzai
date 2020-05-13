<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\DemandeConge,
    AppBundle\Form\DemandeCongeType,
    AppBundle\Entity\DemandeHistoryValidation,
    AppBundle\Form\Search\DemandeCongeSearch,
    AppBundle\Form\DemandeCongeSearchType,
    AppBundle\Constants\GestionCongeConstant,
    AppBundle\Tools\CExportExcelAll,
    AppBundle\Entity\Document,
    AppBundle\Entity\Interim;

/**
  Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
 * DemandeConge controller.
 *
 * @Route("/demande-conge")
 */
class DemandeCongeController extends Controller
{

    /**
     * Lists all DemandeConge entities.
     *
     * @Route("/liste", name="demandeconge_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->getUser()->getFirstConnected() == 0) {
            $route = 'fos_user_change_password';
            return $this->redirectToRoute($route);
        } else {
            $cs = $this->get('common.service');

            //init extraParams
            $extraParams = new \stdClass();
            $demandeCongeSearch = new DemandeCongeSearch();

            //get list id subordonnee for current user
            $subordonneesIds = $cs->getSubordonnees($this->getUser());

            //set params request subordonneesIds
            $extraParams->subordonneesIds = implode(',', $subordonneesIds);
            $extraParams->isAdmin = $this->isGranted('ROLE_ADMIN');

            //create form search demande
            $form = $this->createForm(new DemandeCongeSearchType(), $demandeCongeSearch);
            $form->bind($request);

            $result = $em->getRepository('AppBundle:DemandeConge')->listeDemandeCongeByCriteria($demandeCongeSearch, $extraParams, true);
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $result, $request->query->getInt('page', 1)/* page number */, 10 /* limit per page */
            );

            //$pagination->setUsedRoute('demandeconge_index');
            return $this->render('demandeconge/index.html.twig', array(
                    'pagination' => $pagination,
                    'form' => $form->createView(),
            ));
        }
    }

    /**
     * Creates a new DemandeConge entity.
     *
     * @Route("/new/{menuId}/{userId}", name="demandeconge_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $menuId, $userId)
    {

        $demandeConge = new DemandeConge();
        $currentUser = $this->getUser();
        $demandeConge->setInitiator($currentUser);
        $demandeConge->setDemandor($currentUser);


        $form = $this->createForm(new DemandeCongeType(), $demandeConge, array('menuId' => $menuId, 'userId' => $userId));

        $form->handleRequest($request);
        //DSI-18-00001 : exemple format numero demande
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($demandeConge);
            //si type conge imputé
            if ($demandeConge->getTypeConge()->isImputedSolde()) {
                //decrementer le solde previsionnel du demandeur si solde congé imputé : cas congé payé
                $oDemandor = $demandeConge->getDemandor();
                $oDemandor->setSoldePrevisionnel($oDemandor->getSoldePrevisionnel() - $demandeConge->getTotalDays());
                $em->persist($oDemandor);
            }
            //add list existed interim
            $this->addInterimDemandeConge($demandeConge, $em);
            $this->uploadDocument($demandeConge, $em);
            //valider directement la demande si permission maladie ou absence non justifié
            if ($demandeConge->getTypeConge()->isPermissionMaladie() || $demandeConge->getTypeConge()->isAbsence()) {
                $this->addFlash('success', 'La demande a été bien créée');
                //$demandeConge->setDemandeStatus(GestionCongeConstant::CONGE_STATUS_VALIDATED);
                $em->persist($demandeConge);
                $em->flush();
                // tester si la demande est de type absence non justifié
                if ($demandeConge->getTypeConge()->isAbsence()) {
                    $oDemandor = $demandeConge->getDemandor();
                    $_nbrJours = $demandeConge->getTotalDays();
                    $cs = $this->container->get('common.service');
                    //decrementer le solde reel et previsionnel si une demande est de type absence non justifié
                    $cs->adjustSoldeOnCreateAbsence($oDemandor, $_nbrJours);
                }
                //valider la demande directement
                $demandeConge->setDemandeStatus(GestionCongeConstant::CONGE_STATUS_VALIDATED);
                $em->persist($demandeConge);
                $em->flush();
                //envoyer le mail de notification
                $this->sendMailNotification($demandeConge, $em);

                return $this->redirectToRoute('demandeconge_show', array('id' => $demandeConge->getId()));
            }
            //create history validation demande
            $this->createFisrtStepValidation($demandeConge, $em);
            //send mail notification demande
            $this->sendMailNotification($demandeConge, $em);
            $this->addFlash('success', 'La demande a été bien créée');



            return $this->redirectToRoute('demandeconge_show', array('id' => $demandeConge->getId()));
        }

        return $this->render('demandeconge/new.html.twig', array(
                'demandeConge' => $demandeConge,
                'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a DemandeConge entity.
     *
     * @Route("/{id}", name="demandeconge_show")
     * @Method("GET")
     */
    public function showAction(DemandeConge $demandeConge)
    {

        return $this->render('demandeconge/show.html.twig', array(
                'demandeConge' => $demandeConge,
        ));
    }

    /**
     * set date retour from date end
     * @param Request $request
     * @Route("/demande-conge/date-retour", name="demandeconge_date_retour")
     * @Method("POST")
     *
     * @return type
     */
    public function setDateRetourAction(Request $request)
    {
        $response = new JsonResponse();
        $cs = $this->get('common.service');
        $dcteEnd = new \DateTime($request->request->get('date_retour'));
        $isHalf = $request->request->get('is_half', 0);

        $dcteRetour = $cs->getDateRetour($dcteEnd, $isHalf);
        $response->setData($dcteRetour->format('Y-m-d'));

        return $response;
    }

    /**
     * set total days demande conge
     * @param Request $request
     * @Route("/demande-conge/total-days", name="demandeconge_total_days")
     * @Method("POST")
     *
     * @return type
     */
    public function setTotalDaysAction(Request $request)
    {
        $response = new JsonResponse();
        $cs = $this->get('common.service');
        $dcteStart = new \DateTime($request->request->get('date_start'));
        $dcteEnd = new \DateTime($request->request->get('date_end'));
        $isHalfStart = $request->request->get('is_half_start');
        $isHalfEnd = $request->request->get('is_half_end');
        //print_r(array('start' => $isHalfStart, 'end' => $isHalfEnd));

        $iTotalDays = $cs->calculateNbrJour($dcteStart, $dcteEnd, $isHalfStart, $isHalfEnd);
        $response->setData($iTotalDays);

        return $response;
    }

    /**
     * check chevauchement date demande conge
     * @param Request $request
     * @Route("/demande-conge/check-date", name="demandeconge_check_date")
     *
     * @return JsonResponse
     */
    public function checkDateAction(Request $request)
    {
        $dcta = array('iTotal' => 0, 'result' => null);
        $em = $this->getDoctrine()->getManager();
        $response = new JsonResponse();
        //retrieve all date information current demande
        $dcteStartCurrentDemande = $request->request->get('date_start');
        $dcteEndCurrentDemande = $request->request->get('date_end');
        $iDemandorId = $request->request->get('demandor_id');
        $isHalfDateStartCurrentDemande = $request->request->get('is_half_start');
        $isHalfDateEndCurrentDemande = $request->request->get('is_half_end');

        //get all demande with chevauchement date for current demandor
        $tDemandes = $em->getRepository('AppBundle:DemandeConge')->checkChevauchementDate($dcteStartCurrentDemande, $dcteEndCurrentDemande, $iDemandorId);
        $dcta['iTotal'] = count($tDemandes);
        $dcta['result'] = $tDemandes;
        if ($dcta['iTotal'] > 0) {
            foreach ($tDemandes as $demande) {
                $dcteStart = $demande['date_start'];
                $dcteStartHalfDay = $demande['date_start_afternoon'];
                $dcteEnd = $demande['date_end'];
                $dcteEndHalfDay = $demande['date_end_morning'];
                if (($dcteEnd == $dcteStartCurrentDemande && $dcteEndHalfDay == 1 && $isHalfDateStartCurrentDemande == 1) || ($dcteStart == $dcteEndCurrentDemande && $dcteStartHalfDay == 1 && $isHalfDateEndCurrentDemande == 1)) {
                    $dcta['iTotal'] = 0;
                    break;
                }
            }
        }
        $response->setData($dcta);

        return $response;
    }

    /**
     * planning conge subordonnee calandar mode
     * @param Request $request
     * @Route("/planning-conge/user-{userId}/calandar-mode", name="demandeconge_calandar_mode", defaults={"userId"=0})
     *
     */
    public function planningCongeAction(Request $request, $userId = 0)
    {
        $em = $this->getDoctrine()->getManager();
        //$userId = ($userId > 0) ? $userId : $this->getUser()->getId();
        $url = $this->generateUrl('ajax_demandeconge_planning_mode', array(
            'userId' => $userId
        ));
        $cs = $this->get('common.service');
        $bObjectListNeeded = true;
        $tTypeConge = $em->getRepository('AppBundle:TypeConge')->findBy(array('enabled' => 1));
        //get list id subordonnee for current user, get all user if current user has role ADMIN
        $currentUser = $this->getUser()->hasRole('ROLE_ADMIN') ? null : $this->getUser();
        $tSubordonnees = $cs->getSubordonnees($currentUser, $bObjectListNeeded);
        return $this->render('demandeconge/planning_calandar.html.twig', array(
                'subordonneesList' => $tSubordonnees,
                'eventsUrl' => $url,
                'selectedUserId' => $userId,
                'typeCongeList' => $tTypeConge
        ));
    }

    /**
     * pushDataEvent : push data demande conge to calandar event
     * @param type $toDemandeconges
     *
     * @return array
     */
    private function pushDataEvent($toDemandeconges)
    {
        $arr = array();
        $result = array();

        foreach ($toDemandeconges as $demandeConge) {
            if ($demandeConge instanceof DemandeConge) {
                $arr['id'] = $demandeConge->getId();
                $arr['numero'] = $demandeConge->getNumero();
                $arr['typeconge'] = $demandeConge->getTypeConge()->getCode();
                $arr['demandeur'] = $demandeConge->getDemandor()->getName();
                $arr['dateStart'] = date_format($demandeConge->getDateStart(), 'd/m/Y');
                $arr['start'] = date_format($demandeConge->getDateStart(), 'Y-m-d');
                $arr['totalDays'] = $demandeConge->getTotalDays();
                $arr['dateEnd'] = date_format($demandeConge->getDateEnd(), 'd/m/Y');
                $arr['dateRetour'] = date_format($demandeConge->getDateRetour(), 'd/m/Y');
                $arr['end'] = date_format($demandeConge->getDateEnd(), 'Y-m-d');
                $arr['startdj'] = $demandeConge->getDateStartAfternoon();
                $arr['enddj'] = $demandeConge->getDateEndMorning();


                $iStatus = $demandeConge->getDemandeStatus();
                $style = 'default';
                switch ($iStatus) {
                    case GestionCongeConstant::CONGE_STATUS_VALIDATED:
                        $calcolor = '#00a65a';
                        $zStatus = GestionCongeConstant::CONGE_STATUS_VALIDATED_LABEL;
                        break;
                    case GestionCongeConstant::CONGE_STATUS_REFUSED:
                        $calcolor = '#dd4b39';
                        $zStatus = GestionCongeConstant::CONGE_STATUS_REFUSED_LABEL;
                        break;
                    default:
                        $calcolor = '#3c8dbc';
                        $zStatus = GestionCongeConstant::CONGE_STATUS_INPROGRESS_LABEL;
                        break;
                }

                $arr['status'] = $zStatus;
                $arr['backgroundColor'] = $calcolor;
                $arr['borderColor'] = $calcolor;
                $arr['className'] = $style;
                $arr['url'] = $this->generateUrl('demandeconge_show', array('id' => $demandeConge->getId()));

                $result[] = $arr;
            }
        }

        return $result;
    }

    /**
     * rebuildEvent : rebuild data event
     * @param type $results
     * @return type
     */
    private function rebuildEvent($results)
    {
        $dcta = array();
        $event = array();
        if ($results > 0) {
            foreach ($results as $tmp) {
                $event['id'] = $tmp['id'];
                $event['numero'] = $tmp['numero'];
                $event['title'] = $tmp['typeconge'] . ' : ' . $tmp['demandeur'];
                $event['typeconge'] = $tmp['typeconge'];
                $event['demandeur'] = $tmp['demandeur'];
                $event['start'] = $tmp['start'];
                $event['end'] = $tmp['end'];
                $event['totalDays'] = $tmp['totalDays'];
                $event['status'] = $tmp['status'];
                $event['backgroundColor'] = $tmp['backgroundColor'];
                $event['borderColor'] = $tmp['borderColor'];
                $event['url'] = $tmp['url'];
                $event['dateStart'] = $tmp['dateStart'];
                $event['dateEnd'] = $tmp['dateEnd'];
                $event['dateRetour'] = $tmp['dateRetour'];
                $event['startdj'] = $tmp['startdj'];
                $event['enddj'] = $tmp['enddj'];

                $dcta[] = $event;
            }
        }

        return $dcta;
    }

    /**
     * ajaxLoadPlanning : load demande conge subordonnee in calandar mode
     * @Route("/planning-conge/ajax-load-planning", name="ajax_demandeconge_planning_mode")
     *
     * @return JsonResponse
     */
    public function ajaxLoadPlanningAction(Request $request)
    {
        $response = new JsonResponse();
        $em = $this->getDoctrine()->getManager();
        $cs = $this->get('common.service');
        //get queryString userId from request ajax planning calendar
        $userId = $request->query->get('userId');
        $extraParams = new \stdClass();
        //parameter to get result list but not query
        $bQueryResult = false;
        $oDemandeCongeSearch = new DemandeCongeSearch();
        //get list planning conge all subordonnees currentUser if none user selected
        if ($userId == 0) {
            $currentUser = $this->isGranted('ROLE_ADMIN') ? null : $this->getUser();
            $subordonneesIds = $cs->getSubordonnees($currentUser);
            //set params request subordonneesIds
            $extraParams->subordonneesIds = implode(',', $subordonneesIds);
            $extraParams->isAdmin = $this->isGranted('ROLE_ADMIN');
        } else {
            $oDemandeCongeSearch->setDemandor($userId);
        }
        $toDemandeConges = $em->getRepository('AppBundle:DemandeConge')->listeDemandeCongeByCriteria($oDemandeCongeSearch, $extraParams, $bQueryResult);
        $arrPush = $this->pushDataEvent($toDemandeConges);
        $dcta = $this->rebuildEvent($arrPush);
        $response->setData($dcta);

        return $response;
    }

    /**
     * add interim for demande cinge
     * @param DemandeConge $demandeConge
     * @param type $em
     */
    private function addInterimDemandeConge(DemandeConge $demandeConge, $em)
    {
        $tInterim = $demandeConge->getInterim();
        if (count($tInterim)) {
            foreach ($tInterim as $interim) {
                if ($interim->getDemandeConge() == null) {
                    $interim->setDemandeConge($demandeConge);
                    $interim->setUtilisateur($this->getUser());
                    $em->persist($interim);
                }
            }
        }
    }

    /**
     * upload pj demande conge
     * @param DemandeConge $demandeConge
     *
     * @param type $em
     */
    private function uploadDocument(DemandeConge $demandeConge, $em)
    {
        $tDocument = $demandeConge->getDocument();
        if (count($tDocument)) {
            foreach ($tDocument as $document) {
                if ($document->getDemandeConge() == null) {
                    $document->setDemandeConge($demandeConge);
                    $document->upload($this->container->getParameter('upload_path'));
                    $em->persist($document);
                }
            }
        }
    }

    /**
     * export demande conge
     * @Route("/demande-conge/export-excel", name="demandeconge_export_xls")
     *
     * @return \AppBundle\Controller\Response
     * @throws AccessDeniedException
     */
    public function exportToExcelAction(Request $request)
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        $toAllDatas = array();
        $em = $this->getDoctrine()->getManager();
        $pathXlsFile = $this->getParameter('path_export_dc');
        if (!is_dir($pathXlsFile)) {
            $mode = 0777;
            $bRecursiveMode = true;
            mkdir($pathXlsFile, $mode, $bRecursiveMode);
        }
        $zExportsFileName = "demandes_conges_" . date("YmdHis") . ".xls";
        $zExportsFullPath = $pathXlsFile . $zExportsFileName;
//get list demande to export
        $demandesConges = $this->getListDemandeExport($request, $em);
        if ($demandesConges) {
            $toAllDatas = $this->exportDemandesCongeExcel($request, $zExportsFullPath, $demandesConges);
            if (count($toAllDatas) > 0) {
                CExportExcelAll::createExcelOutput($zExportsFullPath, $toAllDatas['toHeader'], $toAllDatas['toLignesData'], 0);
            }

            if (is_file($zExportsFullPath)) {
                $response = new Response(file_get_contents($zExportsFullPath));
                $response->headers->set('Content-Description', ' File Transfer');
                $response->headers->set('Content-Type', 'application/xls');
                $response->headers->set('Content-Disposition', 'attachment; filename=' . basename($zExportsFullPath));
                $response->headers->set('Content-Transfer-Encoding', 'binary');
                $response->headers->set('Expires', '0');
                $response->headers->set('cache-control', 'must-revalidate');
                $response->headers->set('Pragma', 'public');
                $response->headers->set('Content-Length', filesize($zExportsFullPath));
                $response->setPrivate();
                $response->setMaxAge(15);

                return $response;
            } else {
                return $this->redirect($this->generateUrl("demandeconge_index"));
            }
        } else {
            return $this->redirect($this->generateUrl("demandeconge_index"));
        }
    }

    /**
     * exportDemandesCongeExcel
     * @param type $request
     * @param type $_zExportsFullPath
     * @param type $toDemandesConges
     *
     * @return type
     */
    private function exportDemandesCongeExcel($request, $_zExportsFullPath, $toDemandesConges)
    {
        if ($_zExportsFullPath == '') {
            $_zExportsFullPath = null;
        }
        $toAllDatas = array();
        // Récupération des données
        $toutInfo = 1;
        $avecNumero = $request->get('info_numero');
        $avecDate = $request->get('info_date');
        $avecMatricule = $request->get('info_matricule');
        $avecDatedebut = $request->get('info_datedebut');
        $avecDatefin = $request->get('info_datefin');
        $avecNbrjour = $request->get('info_nombrejourpris');
        $avecDemandeur = $request->get('info_demandeur');
        $avecDirection = $request->get('info_direction');
        $avecEtat = $request->get('info_etat');
        $avecTypeconge = $request->get('info_typeconge');
        $avecSociete = $request->get('info_societe');
        $avecSoldereel = $request->get('info_soldereel');
        $avecDatedebutdj = $request->get('info_datedebutdj');
        $avecDatefindj = $request->get('info_datefindj');

        if ($avecNumero || $avecMatricule || $avecDatedebut || $avecDatefin || $avecNbrjour || $avecDemandeur || $avecDirection || $avecEtat || $avecDemandeur || $avecTypeconge || $avecSociete || $avecSoldereel || $avecDatedebutdj || $avecDatefindj) {
            $toutInfo = 0;
        }

        // Construction contenu du document excel
        // Création titres ou les headers contenus excel
        $tHeader = array();
        if ($toutInfo) {
            $tHeader = array(
                utf8_decode('Numero'),
                utf8_decode('Date'),
                utf8_decode('Matricule'),
                utf8_decode('Date debut'),
                utf8_decode('1/2 J debut'),
                utf8_decode('Date fin'),
                utf8_decode('1/2 J fin'),
                utf8_decode('Nombre de jour pris'),
                utf8_decode('Demandeur'),
                utf8_decode('Direction'),
                utf8_decode('Etat'),
                utf8_decode('Type de congé'),
                utf8_decode('Société'),
                utf8_decode('Solde')
            );
        } else {
            // Les titres choisis par user
            if ($avecNumero) {
                array_push($tHeader, utf8_decode('Numero'));
            }
            if ($avecDate) {
                array_push($tHeader, utf8_decode('Date'));
            }
            if ($avecMatricule) {
                array_push($tHeader, 'Matricule');
            }
            if ($avecDatedebut) {
                array_push($tHeader, 'Date Debut');
            }
            if ($avecDatedebutdj) {
                array_push($tHeader, '1/2 J debut');
            }
            if ($avecDatefin) {
                array_push($tHeader, 'Date fin');
            }
            if ($avecDatefindj) {
                array_push($tHeader, '1/2J fin');
            }
            if ($avecNbrjour) {
                array_push($tHeader, utf8_decode('Nombre de jour pris'));
            }
            if ($avecDemandeur) {
                array_push($tHeader, 'Demandeur');
            }
            if ($avecDirection) {
                array_push($tHeader, 'Direction');
            }
            if ($avecEtat) {
                array_push($tHeader, 'Etat');
            }
            if ($avecTypeconge) {
                array_push($tHeader, utf8_decode('Type de congé'));
            }
            if ($avecSociete) {
                array_push($tHeader, utf8_decode('Société'));
            }
            if ($avecSoldereel) {
                array_push($tHeader, 'Solde');
            }
        }

        // Création des lignes de données
        //$tLigneFilter = array_unique (array());
        $index = 0;
        $tDatas = array();
        foreach ($toDemandesConges as $dc) {
            if ($dc instanceof DemandeConge) {
                $demandeur = !is_null($dc->getDemandor()) ? $dc->getDemandor()->getName() : '';
                $societe = !is_null($dc->getDemandor()) ? $dc->getDemandor()->getName() : ''; //à remplacer par la societe du demandeur
                $soldereel = !is_null($dc->getDemandor()) ? $dc->getDemandor()->getSoldeReel() : '';
                $dctedebutdj = !is_null($dc->getDateStartAfternoon()) ? $dc->getDateStartAfternoon() : '0';
                $dctefindj = !is_null($dc->getDateEndMorning()) ? $dc->getDateEndMorning() : '0';

                $direction = '-';
                $oDemandeur = $dc->getDemandor();
                if ($oDemandeur->getId() !== 0) {
                    $direction = ($dc->getDemandor()->getDirection()) ? $dc->getDemandor()->getDirection()->getSigle() : '';
                }

                if ($dc->getTypeConge()->getId() == '') {
                    $typeconge = '-';
                } else {
                    $typeconge = $dc->getTypeConge()->getLibelle();
                }

                $etat = $dc->getDemandeStatus();
                switch ($etat) {
                    case GestionCongeConstant::CONGE_STATUS_VALIDATED:
                        $etat = GestionCongeConstant::CONGE_STATUS_VALIDATED_LABEL;
                        break;
                    case GestionCongeConstant::CONGE_STATUS_REFUSED:
                        $etat = GestionCongeConstant::CONGE_STATUS_REFUSED_LABEL;
                        break;
                    default:
                        $etat = GestionCongeConstant::CONGE_STATUS_INPROGRESS_LABEL;
                }

                //$tLigneConge = array();

                if ($avecNumero) {
                    $tDatas[$index][] = utf8_decode($dc->getNumero());
                }
                if ($avecDate) {
                    $tDatas[$index][] = utf8_decode(date_format($dc->getCreatedAt(), 'd/m/Y'));
                }
                if ($avecMatricule) {
                    $tDatas[$index][] = utf8_decode($dc->getDemandor()->getMatricule());
                }
                if ($avecDatedebut) {
                    $tDatas[$index][] = utf8_decode(date_format($dc->getDateStart(), 'd/m/Y'));
                }
                if ($avecDatedebutdj) {
                    $tDatas[$index][] = utf8_decode($dctedebutdj);
                }
                if ($avecDatefin) {
                    $tDatas[$index][] = utf8_decode(date_format($dc->getDateEnd(), 'd/m/Y'));
                }
                if ($avecDatefindj) {
                    $tDatas[$index][] = utf8_decode($dctefindj);
                }
                if ($avecNbrjour) {
                    $tDatas[$index][] = utf8_decode(str_replace(".", ",", $dc->getTotalDays()));
                }
                if ($avecDemandeur) {
                    $tDatas[$index][] = utf8_decode($demandeur);
                }
                if ($avecDirection) {
                    $tDatas[$index][] = utf8_decode($direction);
                }
                if ($avecEtat) {
                    $tDatas[$index][] = utf8_decode($etat);
                }
                if ($avecTypeconge) {
                    $tDatas[$index][] = utf8_decode($typeconge);
                }
                if ($avecSociete) {
                    $tDatas[$index][] = utf8_decode($societe);
                }
                if ($avecSoldereel) {
                    $tDatas[$index][] = utf8_decode(str_replace(".", ",", $soldereel));
                }
                $index ++;
            }
        } // fin foreach
        // Filtre cas ou header - Matricule et solde, pour supprimer les doublons infos
        $tTitre = array('Matricule', 'Solde');
        if (count(array_diff($tTitre, $tHeader)) == 0) {
            $tDatas = array_unique($tDatas, SORT_REGULAR);
        }

        $toAllDatas['toHeader'] = $tHeader;
        $toAllDatas['toLignesData'] = $tDatas;

        return $toAllDatas;
    }

    /**
     * download pj demande conge
     * @Route("/download-document/{id}", name="demandeconge_download_document")
     * @ParamConverter("document", options={"mapping": {"id":"id"}})
     *
     * @return \AppBundle\Controller\Response
     * @throws AccessDeniedException
     */
    public function downloadAction(Document $document)
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);

        $basePath = realpath($this->container->getParameter('upload_path') . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $zExportsFileName = $document->getFileName();
        $zExportsFullPath = $basePath . $zExportsFileName;

        if (is_file($zExportsFullPath)) {
            $response = new Response(file_get_contents($zExportsFullPath));
            $response->headers->set('Content-Description', ' File Transfer');
            $response->headers->set('Content-Type', $document->getFileType());
            $response->headers->set('Content-Disposition', 'attachment; filename=' . $document->getFileOriginalName());
            $response->headers->set('Content-Transfer-Encoding', 'binary');
            $response->headers->set('Expires', '0');
            $response->headers->set('cache-control', 'must-revalidate');
            $response->headers->set('Pragma', 'public');
            $response->headers->set('Content-Length', filesize($zExportsFullPath));
            $response->setPrivate();
            $response->setMaxAge(15);

            return $response;
        }

        return $this->redirect($this->generateUrl("demandeconge_show", array('id' => $document->getDemandeConge())));
    }

    /**
     * get list demande conge to export
     * @param Request $request
     * @param type $em
     *
     * @return type
     */
    private function getListDemandeExport(Request $request, $em)
    {
        $cs = $this->get('common.service');
        //init extraParams
        $extraParams = new \stdClass();
        $extraParams->sortField = $request->get('sort_field', null);
        $extraParams->sortDirection = $request->get('sort_direction', null);
        $demandeCongeSearch = $this->prepareFiltersDemandeCongeSearch($request);
        //get list id subordonnee for current user
        $currentUser = $this->isGranted('ROLE_ADMIN') ? null : $this->getUser();
        $subordonneesIds = $cs->getSubordonnees($currentUser);
        //check if current user is admin => get list all demande
        $extraParams->isAdmin = $this->isGranted('ROLE_ADMIN');
        //set params request subordonneesIds
        $extraParams->subordonneesIds = implode(',', $subordonneesIds);
        $demandesConges = $em->getRepository('AppBundle:DemandeConge')->listeDemandeCongeByCriteria($demandeCongeSearch, $extraParams, false);

        return $demandesConges;
    }

    /**
     * prepare object entity DemandeCongeSearch
     * @param Request $request
     *
     * @return DemandeCongeSearch
     */
    private function prepareFiltersDemandeCongeSearch(Request $request)
    {
        $demandeCongeSearch = new DemandeCongeSearch();
        $numero = $request->get('numero_dc');
        $matricule = $request->get('matricule_dc');
        $typeConge = $request->get('type_dc');
        $initiator = $request->get('initiator_dc');
        $dateStart = $request->get('datedebut_dc');
        $dateEnd = $request->get('datefin_dc');
        $demandor = $request->get('demandor_dc');
        //$societe = $request->get('societe_dc');
        $direction = $request->get('direction_dc');
        $etat = $request->get('etat_dc');
        //call setter $demandeCongeSearch
        $demandeCongeSearch->setNumero($numero);
        $demandeCongeSearch->setMatricule($matricule);
        $demandeCongeSearch->setTypeConge($typeConge);
        $demandeCongeSearch->setInitiator($initiator);
        $demandeCongeSearch->setDateStart($dateStart);
        $demandeCongeSearch->setDateEnd($dateEnd);
        $demandeCongeSearch->setDemandor($demandor);
        $demandeCongeSearch->setDemandeStatus($etat);
        //$demandeCongeSearch->setSociete($societe);
        $demandeCongeSearch->setDirection($direction);

        return $demandeCongeSearch;
    }

    /**
     * createFisrtStepValidation : add manager as validator if validatin type is manager
     * @param DemandeConge $oDemandeConge
     * @param type $em
     */
    private function createFisrtStepValidation(DemandeConge $oDemandeConge, $em)
    {
        //create first step validation
        $demandeHistoryValidation = new DemandeHistoryValidation();
        $demandeHistoryValidation->setDemandeConge($oDemandeConge);
        //recuperer la premiere etape de validation
        //$step = $oDemandeConge->getModeleWorkflow()->getModeleWorkflowSteps()->first();
        $step = $oDemandeConge->getTypeConge()->getWorkflowType()->getModeleWorkflowSteps()->first();
        $demandeHistoryValidation->setModeleWorkflowStep($step);
        //add manager as validator if validation type is manager
        if ($step->isManagerValidation()) {
            /* get default validator => manager currentUser
              $oManagerCurrentUser = $this->getUser()->getManager(); */
            //get manager demandor
            $oManager = $oDemandeConge->getDemandor()->getManager();
            //set validator demande
            //$demandeHistoryValidation->setValidator($oManagerCurrentUser);
            $demandeHistoryValidation->setValidator($oManager);
        }
        //persist $demandeHi storyValidation object
        $em->persist($demandeHistoryValidation);
        $em->flush();
    }

    /**
     * ajaxTransfertValidationAction : transfert de validation de la  demande conge via ajax
     * @Route("/demande-conge/transfert-validation", name="demandeconge_transfert_validation")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function ajaxTransfertValidationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $response = new JsonResponse();
        $data = array('msg' => 'ok', 'status' => 200);
        if ($request->isXmlHttpRequest()) {
            $iDemandeCongeId = $request->request->get('demandeId');
            $iInterim = $request->request->get('interimId');
            if (!$this->isInterimAlreadySaved($iDemandeCongeId, $iInterim, $em)) {
                //create new interim validation
                $this->createInterim($iDemandeCongeId, $iInterim, $em);
            } else {
                $data = array('msg' => 'ko', 'status' => 409);
            }
        }
        $response->setData($data);

        return $response;
    }

    /**
     * check if interim already exists in demande
     * @param type $demandeId
     * @param type $interimId
     * @param type $em
     *
     * @return boolean
     */
    private function isInterimAlreadySaved($demandeId, $interimId, $em)
    {
        $oInterim = $em->getRepository('AppBundle:Interim')->findOneBy(array('demandeConge' => $demandeId, 'interim' => $interimId, 'enabled' => 1));
        if ($oInterim) {

            return true;
        }

        return false;
    }

    /**
     * create new interim in database
     * @param type $iDemandeCongeId
     * @param type $iInterim
     * @param type $em
     */
    private function createInterim($iDemandeCongeId, $iInterim, $em)
    {
        $oInterim = new Interim();
        $fromDate = new \DateTime();
        $oInterim->setInterimType(GestionCongeConstant::INTERIM_TYPE_VALIDATION);
        $userInterim = $em->getRepository('AdminUserBundle:Utilisateur')->find($iInterim);
        $oDemandeConge = $em->getRepository('AppBundle:DemandeConge')->find($iDemandeCongeId);
        $oInterim->setUtilisateur($this->getUser());
        $oInterim->setInterim($userInterim);
        $oInterim->setDemandeConge($oDemandeConge);
        $oInterim->setFromDate($fromDate);
        $oInterim->setToDate($oDemandeConge->getDateEnd());
        $em->persist($oInterim);
        $em->flush();
    }

    /**
     * Send mail notification after create demande
     * @param type demandeconge
     * @param type $em
     */
    private function sendMailNotification(DemandeConge $demandeConge, $em)
    {

        $mailer = $this->get('mailer');
        $cs = $this->get('common.service');
        // get numero demande
        $numero = $demandeConge->getNumero();

        // get email of initiator and demandor
        $emailDemandeur = !is_null($demandeConge->getDemandor()) ? $demandeConge->getDemandor()->getEmail() : '';
        $emailInitiateur = !is_null($demandeConge->getInitiator()) ? $demandeConge->getInitiator()->getEmail() : '';

        // get solde of current user
        $oUserConcerne = $em->getRepository('AdminUserBundle:Utilisateur')->find($demandeConge->getDemandor());
        if (!is_null($oUserConcerne)) {
            $soldeReelNotification = $oUserConcerne->getSoldeReel();
            $soldePrevisionnelNotification = $oUserConcerne->getSoldePrevisionnel();
        }

        $objet = 'Enregistrement d\'une demande de congé numéro : ' . $numero;
        $fromTo = array($oUserConcerne->getEmail() => $oUserConcerne->getName());
        $setTo = array();

        array_push($setTo, $emailInitiateur);

        if ($emailDemandeur != '' && !in_array($emailDemandeur, $setTo)) {
            array_push($setTo, $emailDemandeur);
        }
		
		$manager = $em->getRepository('AdminUserBundle:Utilisateur')->find($demandeConge->getDemandor()->getManager());
        if ($manager) {
            if (!in_array($manager->getEmail(), $setTo))
                array_push($setTo, $manager->getEmail());
        }

        //get list interim of manager
        $tInterimIds = $em->getRepository('AppBundle:Interim')->getListInterimByValidator($demandeConge);
        if (sizeof($tInterimIds) > 0) {
            foreach ($tInterimIds as $interimId) {
                $interim = $em->getRepository('AdminUserBundle:Utilisateur')->findOneById($interimId);
                if (!in_array($interim->getEmail(), $setTo)) {
                    array_push($setTo, $adminRh->getEmail());
                }
            }
        }
		

        // check if type of demand is absence
        if ($demandeConge->getTypeConge()->isPermissionMaladie() || $demandeConge->getTypeConge()->isAbsence()) {
            
            //check all Admin RH
            $tAdminRh = $em->getRepository('AdminUserBundle:Utilisateur')->findAll();
            if (sizeof($tAdminRh) > 0) {
                foreach ($tAdminRh as $adminRh) {
                    if ($adminRh->hasRole('ROLE_ADMIN') && !in_array($adminRh->getEmail(), $setTo)) {
                        if (!in_array($adminRh->getEmail(), $setTo))
                            array_push($setTo, $adminRh->getEmail());
                    }
                }
            }
            $content = $this->renderView('demandeconge:BoDemandescongesMail.html.twig', array('demandeconge' => $demandeConge, 'type' => 1, 'soldeReel' => $soldeReelNotification, 'soldePrevisionnel' => $soldePrevisionnelNotification));
            // Envoi mail au initiateur et demandeur
            $cs->sendEmailTo($objet, $fromTo, $setTo, $content, $mailer);
        }
        else {
		
            $objet = 'Nouvelle demande de conge numéro : ' . $numero;
            //$sendTo = array();
            //$sendTo = 'rjclement.ramamonjisoa@gmail.com'; //$emailsDelegations;
            //$sendTo = $emailsDelegations;
            $datas = array(
                'demandeconge' => $demandeConge,
                //'nextWfDahistoryId' => $nextWfDahistoryId,
                'type' => 3,
                'soldeReel' => $soldeReelNotification,
                'soldePrevisionnel' => $soldePrevisionnelNotification
            );
            $contentToValidateur = $this->renderView(':demandeconge:BoDemandescongesMail.html.twig', $datas);
            $cs->sendEmailTo($objet, $fromTo, $setTo, $contentToValidateur, $mailer);
        }
    }

}
