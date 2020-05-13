<?php

namespace AppBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Constants\GestionCongeConstant;

/**
 * CommonService
 * call common.service
 */
class CommonService
{
    private $container;
    private $em;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    /**
     * calculateNbrJour : calcul nombre de jours entre deux dates sans compter les weekends et les jours feries
     * @param type $dateStart
     * @param type $dateEnd
     * @return float
     */
    public function calculateNbrJour($dateStart, $dateEnd, $bHalfDayStart = 0, $bHalfDayEnd = 0)
    {
        $iDays = 0;
        $iWeek = 0;
        $curr = 0;
//inclure la date de fin dans le calcul
        $dateEnd->modify('+1 day');

        $interval = $dateEnd->diff($dateStart);

// total days
        $iDays = $interval->days;
// create an iterateable period of date (P1D equates to 1 day)
        $period = new \DatePeriod($dateStart, new \DateInterval('P1D'), $dateEnd);

//liste des jour feries legal à recupérer de la param de config
        $holidaysList = $this->container->getParameter('holidays_list');
//liste des jours feries variables à recuperer de la base des données
        $joursFeriesList = $this->getListJoursFeries();

        foreach ($period as $dt) {
            $curr = $dt->format('N');
//si samedi et dimanche
            /* if ($curr == 6 || $curr == 7) {
              $iDays--;
              } */


// skip date in array $holidaysList and $joursFeriesList
            //elseif (in_array($dt->format('Y-m-d'), $joursFeriesList) || in_array($dt->format('d-m'), $holidaysList)) {
            if (in_array($dt->format('Y-m-d'), $joursFeriesList) || in_array($dt->format('d-m'), $holidaysList)) {
                $iDays--;
            }
        }

        //si l'utilisateur choisi demi journée pour la date de départ et démi journée pour la date de fin
        if ($bHalfDayStart > 0 && $bHalfDayEnd > 0) {
            $iDays = $iDays - 1;
        } elseif ($bHalfDayStart > 0 || $bHalfDayEnd > 0) {
            //si demi journée début ou démi journée pour la date de fin
            $iDays = $iDays - 0.5;
        }

        return $iDays;
    }

    /**
     * getDateRetour determiner la date de retour en fonction de la date de fin
     * @param type $dateEnd
     * @param type $bHalf
     *
     * return date
     */
    public function getDateRetour($dateEnd, $bHalf = false)
    {
        if ($bHalf) {
            return $dateEnd;
        }
        $dateRetour = $dateEnd->modify('+1 days');
        if ($this->isWeekend($dateRetour) || $this->isFerie($dateRetour)) {
            $dateRetour = $this->getDateRetour($dateRetour);
        }

        return $dateRetour;
    }

    /**
     * list de tous les jours feries
     * @return array
     */
    public function getListJoursFeries()
    {
        $joursferiesRepository = $this->em->getRepository('AppBundle:JoursFeries');
        $aJoursFeries = $joursferiesRepository->getListJoursFeries();

        return $aJoursFeries;
    }

    /**
     * check if date is weekend
     * @param type $date
     *
     * return boolean
     */
    public function isWeekend($date)
    {
        $dayNumber = $date->format('N');
        //si samedi et dimanche
        return ($dayNumber == 6 || $dayNumber == 7);
    }

    /**
     * check if date is ferie
     * @param type $date
     *
     * return boolean
     */
    public function isFerie($date)
    {
        //liste des jour feries legal à recupérer de la param de config
        $holidaysList = $this->container->getParameter('holidays_list');
        //liste des jours feries variables à recuperer de la base des données
        $joursFeriesList = $this->getListJoursFeries();

        // check if date in array $holidaysList and $joursFeriesList
        return (in_array($date->format('Y-m-d'), $joursFeriesList) || in_array($date->format('d-m'), $holidaysList));
    }

    /**
     * liste subordonnee for given user
     * @param type $result
     * @param type $oUser
     */
    public function listeSubordonnee(&$result, $oUser)
    {
        //if current user has role admin
        if (!is_null($oUser)) {
            $result[] = $oUser;
        }
        $subordonnes = $this->em->getRepository('AdminUserBundle:Utilisateur')->listeSubordonnee($oUser);
        if (count($subordonnes) > 0) {
            foreach ($subordonnes as $subordonne) {
                $result[] = $subordonne;
                self::listeSubordonnee($result, $subordonne);
            }
        }

        //return $result;
    }

    /**
     * get list id subordonnees
     * @param type $oUser
     *
     * @return array
     */
    public function getSubordonnees($oUser, $isObject = false)
    {
        $tUserIds = array();
        $toUsers = array();
        $result = array();
        $this->listeSubordonnee($result, $oUser);
        if (count($result) > 0) {
            foreach ($result as $oUser) {
                if (!in_array($oUser->getId(), $tUserIds)) {
                    array_push($tUserIds, $oUser->getId());
                    array_push($toUsers, $oUser);
                }
            }
        }
        return $isObject ? $toUsers : $tUserIds;
    }

    /**
     * adjustSoldeOnCreateJoursFeries
     * @param type $joursFerieDate
     */
    public function adjustSoldeOnCreateJoursFeries($joursFerieDate)
    {
        $isQueryToReturn = false;
        $demandeCongeSearch = null;
        $extraParams = new \stdClass();
        $extraParams->joursFeies = $joursFerieDate;
        $aoDemandes = $this->em->getRepository('AppBundle:DemandeConge')->listeDemandeCongeByCriteria($demandeCongeSearch, $extraParams, $isQueryToReturn);
        if (count($aoDemandes)) {
            $this->adjustSoldeCongeFerie($aoDemandes);
        }
    }

    /**
     * adjustSoldeOnCreateAbsence
     * @param type $currentUser
     * @param type $_nbrJours
     */
    public function adjustSoldeOnCreateAbsence($currentUser, $_nbrJours)
    {
        $isQueryToReturn = false;
        $utilisateurSearch = null;
        $extraParams = new \stdClass();
        $extraParams->absence = $currentUser->getUsername();
        $aoUsers = $this->em->getRepository('AdminUserBundle:Utilisateur')->listeUserByCriteria($utilisateurSearch, $extraParams, $isQueryToReturn);
        if (count($aoUsers)) {
            $this->adjustSoldeAbsence($aoUsers, $_nbrJours);
        }
    }

    /**
     * adjustSoldeCongeFerie
     * @param type $aoDemandes
     * @param type $_nbrJours
     */
    private function adjustSoldeCongeFerie($aoDemandes)
    {
        foreach ($aoDemandes as $oDemande) {
            $bImputedSoldeTypeDemande = $oDemande->getTypeConge()->isImputedSolde();
            $oDemandor = $oDemande->getDemandor();
            $iStatusDemande = $oDemande->getDemandeStatus();
            //si solde réel imputé (congé payé) et que le statut de la demande est déjà validé
            if ($bImputedSoldeTypeDemande && $iStatusDemande) {
                $_nbrJours = ($oDemande->getDateStartAfternoon() || $oDemande->getDateEndMorning()) ? 0.5 : 1;
                $oDemandor->setSoldePrevisionnel($oDemandor->getSoldePrevisionnel() + $_nbrJours);
                $oDemandor->setSoldeReel($oDemandor->getSoldeReel() + $_nbrJours);
                $this->em->persist($oDemandor);
                $this->em->flush();
            }
        }
    }

    /**
     * adjustSoldeAbsence
     * @param type $aoUsers
     * @param type $_nbrJours
     */
    private function adjustSoldeAbsence($aoUsers, $_nbrJours)
    {
        foreach ($aoUsers as $oUser) {
            //si solde réel et prévisonnel imputé en cas d'absence
            if ($oUser) {
                $oUser->setSoldePrevisionnel($oUser->getSoldePrevisionnel() - $_nbrJours);
                $oUser->setSoldeReel($oUser->getSoldeReel() - $_nbrJours);
                $this->em->persist($oUser);
                $this->em->flush();
            }
        }
    }

    /**
     * Envoir de mail
     *
     * @param string $objet
     * @param string $expeditaire
     * @param string $destinataire
     * @param string $content
     * @param string $mailer
     *
     * @return null
     *
     */
    public function sendEmailTo($objet, $expeditaire, $destinataire, $content, $mailer)
    {
        $message = \Swift_Message::newInstance()->setSubject($objet)
                ->setFrom($expeditaire)->setTo($destinataire)
                ->setContentType("text/html")->setBody($content);
        try {
            $mailer->send($message);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die;
        }
    }

}
