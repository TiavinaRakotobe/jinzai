<?php

namespace Admin\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Security,
    Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Form\Search\UtilisateurSearch,
    Admin\UserBundle\Entity\Utilisateur,
    Admin\UserBundle\Form\Type\UtilisateurSearchFormType,
    Admin\UserBundle\Form\Type\RegistrationFormType;

class UtilisateurController extends Controller
{

    /**
     * List users
     *
     * @Route("/utilisateur/liste", name="user_liste")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //init extraParams
        $extraParams = new \stdClass();
        $utilisateurSearch = new UtilisateurSearch();

        //create form search demande
        $form = $this->createForm(new UtilisateurSearchFormType(), $utilisateurSearch);
        $form->bind($request);

        $tUsers = $em->getRepository('AdminUserBundle:Utilisateur')->listeUserByCriteria($utilisateurSearch, $extraParams, true);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $tUsers, $request->query->getInt('page', 1)/* page number */, 10/* limit per page */
        );

        return $this->render('AdminUserBundle:Utilisateur:index.html.twig', array(
                'pagination' => $pagination,
                'form' => $form->createView(),
                'users' => $tUsers,
        ));
    }

    /**
     * Creates a new JoursFeries entity.
     *
     * @Route("/utilisateur/editer/{id}", name="edit_user", defaults={"id"= null})
     * @ParamConverter("user", options={"mapping": {"id":"id"}})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Utilisateur $user = null)
    {
        $form = $this->createForm(new RegistrationFormType(), $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            //$cs = $this->container->get('common.service');
            //incrementer le solde reel et previsionnel si une demande � cette date a �t� d�j� valid�e
            //$cs->adjustSoldeOnCreateJoursFeries($joursferies->getJoursFeriesDate());

            return $this->redirectToRoute('user_liste');
            //return $this->redirectToRoute('joursferies_show', array('id' => $joursferies->getId()));
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:register.html.twig', array(
                'form' => $form->createView(),
                'action' => $this->generateUrl("edit_user", array('id' => $user->getId()))
        ));
    }

    /**
     * search user detail entity.
     *
     * @Route("/utilisateur/detail", name="search_user_detail")
     * @Method("POST")
     */
    public function detailAction(Request $request)
    {
        $sMatricule = "";
        if ($request->isMethod("post")) {
            $em = $this->getDoctrine()->getManager();
            $sMatricule = $request->request->get('matricule');
            $oUser = $em->getRepository("AdminUserBundle:Utilisateur")->findOneBy(array('matricule' => $sMatricule));

            return $oUser ? $this->redirectToRoute("edit_user", array(
                    'id' => $oUser->getId(),
                    'matricule' => $sMatricule)) : $this->render('AdminUserBundle:Utilisateur:result.html.twig', array(
                    'matricule' => $sMatricule
            ));
        }
    }

    /**
     * reset password user entity by admin.
     *
     * @Route("/utilisateur/reset/{id}", name="user_reset_password", defaults={"id"= null})
     * @ParamConverter("user", options={"mapping": {"id":"id"}})
     */
    public function resetPasswordAction(Request $request, Utilisateur $user = null)
    {
        if (!is_null($user)) {
            $em = $this->getDoctrine()->getManager();
            $user->setPlainPassword('demo123'); //set password to default value
            $user->setFirstConnected(0);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Password reseted for user ' . $user->getName());
        }

        return $this->redirectToRoute("user_liste");
    }

}
