<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DirectionType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sigle', null, array(
                'attr' => array(
                    'class' => 'form-control'
                ),
                'label' => 'Sigle'
            ))
            ->add('libelle', null, array(
                'attr' => array(
                    'class' => 'form-control'
                ),
                'label' => 'Nom direction'
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
            'data_class' => 'AppBundle\Entity\Direction'
        ));
    }

}
