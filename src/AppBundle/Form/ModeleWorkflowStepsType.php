<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use AppBundle\Constants\GestionCongeConstant;

class ModeleWorkflowStepsType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', 'text', array(
                'label' => 'Libellé:',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                    'title' => "Libellé étape"))
            )
            ->add('stepOrder', null, array(
                'label' => 'Ordre',
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('validationType', 'choice', array(
                'choices' => array(
                    GestionCongeConstant::VALIDATION_MANAGER => GestionCongeConstant::VALIDATION_MANAGER_LABEL,
                    GestionCongeConstant::VALIDATION_RH => GestionCongeConstant::VALIDATION_RH_LABEL,
                    GestionCongeConstant::CUSTOM_VALIDATION => GestionCongeConstant::CUSTOM_VALIDATION_LABEL
                ),
                'label' => 'Validation type ',
                'expanded' => false,
                'attr' => array(
                    'class' => 'form-control',
                    'title' => "Validation type "
                ),
                'empty_data' => '',
                'empty_value' => ''
            ))
        /* ->add('wfStepValidator', 'collection', array(
          'type' => new ModeleWorkflowStepsValidatorType(),
          'allow_add' => true,
          'allow_delete' => true,
          'by_reference' => false,
          'required' => true,
          )) */
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ModeleWorkflowSteps'
        ));
    }

}
