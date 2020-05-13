<?php

namespace AppBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Constants\GestionCongeConstant,
    AppBundle\Entity\DemandeConge;
use Admin\UserBundle\Entity\Utilisateur;

/**
 * CommonTwigExtension
 */
class CommonTwigExtension extends \Twig_Extension
{
    private $container;
    private $em;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    public function getFilters()
    {
        return array(
            'label_status_dc' => new \Twig_Filter_Method($this, 'getStringStatus'),
            'label_interim' => new \Twig_Filter_Method($this, 'getLabelInterimType'),
        );
    }

    /**
     *
     * @return type
     */
    public function getFunctions()
    {
        return array(
            'getValidatorStep' => new \Twig_Function_Method($this, 'getValidatorStep'),
            'listUsersBySociety' => new \Twig_Function_Method($this, 'listUsersBySociety'),
            'countPermission' => new \Twig_Function_Method($this, 'countPermission'),
            'can' => new \Twig_Function_Method($this, 'can'),
        );
    }

    /**
     * get all validators for stepId
     * @param type $stepId
     *
     * @return type
     */
    public function getValidatorStep($stepId)
    {
        return $this->em->getRepository('AppBundle:ModeleWorkflowStepsValidator')->findBy(array('modeleWorkflowStepsId' => $stepId));
    }

    /**
     * listUsersBySociety
     * @param type $societyId
     *
     * @return type
     */
    public function listUsersBySociety($societyId = null)
    {
        return $this->em->getRepository('AdminUserBundle:Utilisateur')->findBy(array('enabled' => 1));
    }

    /**
     * get chaine statut congé
     * @param type $iStatus
     *
     * @return string
     */
    public function getStringStatus($iStatus)
    {
        switch ($iStatus) {
            case GestionCongeConstant::CONGE_STATUS_INPROGRESS:
                $zStatus = GestionCongeConstant::CONGE_STATUS_INPROGRESS_LABEL;
                break;
            case GestionCongeConstant::CONGE_STATUS_VALIDATED:
                $zStatus = GestionCongeConstant::CONGE_STATUS_VALIDATED_LABEL;
                break;
            case GestionCongeConstant::CONGE_STATUS_REFUSED:
                $zStatus = GestionCongeConstant::CONGE_STATUS_REFUSED_LABEL;
                break;
            default:
                $zStatus = GestionCongeConstant::CONGE_STATUS_INPROGRESS_LABEL;
        }

        return $zStatus;
    }

    /**
     * get label interim type
     * @param type $iTypeInterim
     *
     * @return type
     */
    public function getLabelInterimType($iTypeInterim)
    {
        switch ($iTypeInterim) {
            case GestionCongeConstant::INTERIM_TYPE_FONCTION:
                $zInterim = GestionCongeConstant::INTERIM_TYPE_FONCTION_LABEL;
                break;
            case GestionCongeConstant::INTERIM_TYPE_VALIDATION:
                $zInterim = GestionCongeConstant::INTERIM_TYPE_VALIDATION_LABEL;
                break;
            default :
                $zInterim = GestionCongeConstant::INTERIM_TYPE_VALIDATION_LABEL;
        }

        return $zInterim;
    }

    /**
     * count permission exceptionnelle user
     * @param type $oUser
     * @return type
     */
    public function countPermission($oUser)
    {
        return $this->em->getRepository('AppBundle:DemandeConge')->getTotalPermissionUser($oUser->getId());
    }

    /**
     * can : check if user can do action in demande
     * @param type $oUser
     * @param DemandeConge $oDemandeConge
     *
     * @return boolean
     */
    public function can($sAction, Utilisateur $oUser, DemandeConge $oDemandeConge)
    {
        switch ($sAction) {
            case 'transfert':
                $bCan = ($oUser->hasRole('ROLE_ADMIN') && $oDemandeConge->getDemandeStatus() == GestionCongeConstant::CONGE_STATUS_INPROGRESS);
                break;
            case 'validate': {
                    $oDemandor = $oDemandeConge->getDemandor();
                    $bCan = ($oDemandor->getId() != $oUser->getId() || is_null($oDemandor->getManager()));
                    break;
                }
            default :
                $bCan = false;
        }

        return $bCan;
    }

    public function canValidate()
    {

    }

    /**
     * Set a name for the extension
     */
    public function getName()
    {
        return 'twig_extension';
    }

}
