<?php

namespace AppBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;

class DemandeHistoryValidationSearchType extends DemandeCongeSearchType
{

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Form\Search\DemandeHistoryValidationSearch'
        ));
    }

}
