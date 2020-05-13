<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Constants\GestionCongeConstant;
use Doctrine\ORM\EntityRepository;

class TypeCongeType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', null, array(
                'attr' => array(
                    'class' => 'form-control'
                ),
                'label' => 'Libellé '
            ))
            ->add('code', null, array(
                'attr' => array(
                    'class' => 'form-control'
                ),
                'label' => 'Code '
            ))
            ->add('color', null, array(
                'attr' => array(
                    'class' => 'form-control colorpicker'
                ),
                'label' => 'Couleur '
            ))
            ->add('imputationType', 'choice', array(
                'choices' => array(
                    GestionCongeConstant::CONGE_PAYE => GestionCongeConstant::CONGE_PAYE_LABEL,
                    GestionCongeConstant::PERMISSION_EXCEPTIONNELLE => GestionCongeConstant::PERMISSION_EXCEPTIONNELLE_LABEL,
                    GestionCongeConstant::PERMISSION_MALADIE => GestionCongeConstant::PERMISSION_MALADIE_LABEL,
                    GestionCongeConstant::ABSENCE_NON_JUSTIFIE => GestionCongeConstant::ABSENCE_NON_JUSTIFIE_LABEL,
                    GestionCongeConstant::DEMANDE_RH => GestionCongeConstant::DEMANDE_RH_LABEL
                ),
                'label' => 'Imputation solde ',
                'expanded' => false,
                'attr' => array(
                    'class' => 'form-control',
                    'title' => "Imputation solde"
                ),
                'empty_data' => '',
                'empty_value' => ''
            ))
            ->add('workflowType', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'class' => 'AppBundle:ModeleWorkflow',
                'empty_data' => '',
                'empty_value' => '',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('W')
                        ->where('W.enabled = 1');
                },
                'property' => 'libelle',
                'expanded' => false,
                'multiple' => false,
                'label' => 'Type workflow ',
                'required' => true,
                'attr' => array(
                    'title' => "Type workflow",
                    'class' => 'form-control'
                ))
            )
            ->add('value', null, array(
                'label' => 'Nombre de jour ',
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('category', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'class' => 'AppBundle:CategoryDemande',
                'empty_data' => '',
                'empty_value' => '',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('C')
                        ->where('C.enabled = 1');
                },
                'property' => 'libelle',
                'expanded' => false,
                'multiple' => false,
                'label' => 'Catégorie demande ',
                'required' => true,
                'attr' => array(
                    'title' => "Catégorie de la demande",
                    'class' => 'form-control'
                ))
            )
            ->add('enabled', null, array(
                'attr' => array(
                    'class' => 'checkbox icheck'
                ),
                'label' => 'Activé '
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\TypeConge'
        ));
    }

}
