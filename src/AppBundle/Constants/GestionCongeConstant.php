<?php

namespace AppBundle\Constants;

class GestionCongeConstant
{
    const CONGE_STATUS_INPROGRESS = 0;
    const CONGE_STATUS_VALIDATED = 1;
    const CONGE_STATUS_REFUSED = 2;
    const CONGE_STATUS_CANCELLED = -1;
    const CONGE_STATUS_CANCELINPROGRESS = -2;
    const CONGE_STATUS_INPROGRESS_LABEL = 'En cours';
    const CONGE_STATUS_VALIDATED_LABEL = 'Validée';
    const CONGE_STATUS_REFUSED_LABEL = 'Refusée';
    const CONGE_STATUS_CANCELLED_LABEL = 'Annulée';
    const CONGE_STATUS_CANCELINPROGRESS_LABEL = 'Annulation en cours';
    //Steps order for the validation
    const CONGE_VALIDATION_ORDER_N1 = 0;
    const CONGE_VALIDATION_ORDER_RH = 1;
    //interim
    const INTERIM_TYPE_FONCTION = 1;
    const INTERIM_TYPE_VALIDATION = 2;
    const INTERIM_TYPE_FONCTION_LABEL = 'FONCTION';
    const INTERIM_TYPE_VALIDATION_LABEL = 'VALIDATION';
    //type demande conge ou RH
    const DEMANDE_RH = 0;
    const CONGE_PAYE = 1;
    const PERMISSION_EXCEPTIONNELLE = 2;
    const PERMISSION_MALADIE = 3;
    const ABSENCE_NON_JUSTIFIE = 4;
    const DEMANDE_RH_LABEL = "Demande RH";
	const DEMANDE_RH_STATUS_INPROGRESS = 0;
	
    const CONGE_PAYE_LABEL = "Congé payé";
    const PERMISSION_EXCEPTIONNELLE_LABEL = "Permission exceptionnelle";
    const PERMISSION_MALADIE_LABEL = "Permission maladie";
    const ABSENCE_NON_JUSTIFIE_LABEL = "Absence non justifié";
    //validation type
    const VALIDATION_MANAGER = 1;
    const VALIDATION_RH = 2;
    const CUSTOM_VALIDATION = 3;
    const VALIDATION_MANAGER_LABEL = "Manager";
    const VALIDATION_RH_LABEL = "RH";
    const CUSTOM_VALIDATION_LABEL = "à Personnaliser";

}
