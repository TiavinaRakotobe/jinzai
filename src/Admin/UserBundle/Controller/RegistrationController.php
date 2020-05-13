<?php

/**
 *
 */

namespace Admin\UserBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
    Symfony\Component\Security\Core\Exception\AccessDeniedException,
    Symfony\Component\Security\Core\Exception\AccountStatusException;
use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Controller managing the registration
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 * @Security("has_role('ROLE_ADMIN')")
 */
class RegistrationController extends ContainerAware
{

    public function registerAction()
    {

        $form = $this->container->get('fos_user.registration.form');
        $formHandler = $this->container->get('fos_user.registration.form.handler');
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');


        $process = $formHandler->process($confirmationEnabled);
        if ($process) {
            $user = $form->getData();

            $authUser = false;

            if ($confirmationEnabled) {
                $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $route = 'fos_user_registration_check_email';
            } else {
                $authUser = true;
                $route = 'user_liste';
            }
            $this->setFlash('success', 'L\'utilisateur a été bien crée');
            $url = $this->container->get('router')->generate($route);
            $response = new RedirectResponse($url);

            //don't authenticate user automatically
            /* if ($authUser) {
              $this->authenticateUser($user, $response);
              } */

            return $response;
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:register.html.' . $this->getEngine(), array(
                'form' => $form->createView(),
        ));
    }

    /**
     * @param string $action
     * @param string $value
     */
    protected function setFlash($action, $value)
    {
        $this->container->get('session')->getFlashBag()->set($action, $value);
    }

    protected function getEngine()
    {
        return $this->container->getParameter('fos_user.template.engine');
    }

}
