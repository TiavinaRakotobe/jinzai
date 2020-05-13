<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JoursFeriesType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('joursFeriesDate', 'datetime', array(
                'attr' => array(
                    'class' => 'form-control datepicker'
                ),
                'label' => 'Date',
                'widget' => 'single_text',
                'format' => 'y-M-d'
            ))
            ->add('description', null, array(
                'attr' => array(
                    'class' => 'form-control'
                ),
                'label' => 'Description'
            ))
            ->add('enabled', null, array(
                'attr' => array(
                    'class' => 'checkbox icheck'
                ),
                'label' => 'Activé'
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\JoursFeries'
        ));
    }

}
