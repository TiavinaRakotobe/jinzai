<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use AppBundle\Constants\GestionCongeConstant;

class DemandeRhSearchType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('typeDemande', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'class' => 'AppBundle:TypeConge',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('T')
                        ->where('T.enabled = 1 AND T.imputationType = ' . GestionCongeConstant::DEMANDE_RH);
                },
                'empty_data' => '',
                'empty_value' => '',
                'property' => 'libelle',
                'expanded' => false,
                'multiple' => false,
                'label' => 'Type demande',
                'required' => false,
                'attr' => array(
                    'title' => "Type demande",
                    'class' => 'form-control select2'
                ))
            )
            ->add('demandor', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'class' => 'AdminUserBundle:Utilisateur',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('U')
                        ->where('U.enabled = 1');
                },
                'empty_data' => '',
                'empty_value' => '',
                'property' => 'name',
                'expanded' => false,
                'multiple' => false,
                'label' => 'Demandeur',
                'required' => false,
                'attr' => array(
                    'title' => "Demandeur",
                    'class' => 'form-control select2'
                ))
            )
            ->add('toDate', 'Symfony\Component\Form\Extension\Core\Type\DateType', array(
                'label' => 'Date de début',
                'attr' => array(
                    'class' => 'form-control datepicker date-range-start',
                    'placeholder' => 'Date de début'
                ),
                'required' => false,
                'widget' => 'single_text',
                'format' => 'y-MM-dd'
                )
            )
            ->add('fromDate', 'Symfony\Component\Form\Extension\Core\Type\DateType', array(
                'label' => 'Date de fin',
                'attr' => array(
                    'class' => 'form-control datepicker date-range-start',
                    'placeholder' => 'Date de fin'
                ),
                'required' => false,
                'widget' => 'single_text',
                'format' => 'y-MM-dd'
                )
            )
            ->add('demandeStatus', 'choice', array(
                'choices' => array(
                    GestionCongeConstant::CONGE_STATUS_INPROGRESS => GestionCongeConstant::CONGE_STATUS_INPROGRESS_LABEL,
                    GestionCongeConstant::CONGE_STATUS_VALIDATED => GestionCongeConstant::CONGE_STATUS_VALIDATED_LABEL,
                    GestionCongeConstant::CONGE_STATUS_REFUSED => GestionCongeConstant::CONGE_STATUS_REFUSED_LABEL
                ),
                'label' => 'Statut ',
                'expanded' => false,
                'attr' => array(
                    'class' => 'form-control',
                    'title' => "Statut"
                ),
                'required' => false,
                'empty_data' => '',
                'empty_value' => ''
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Form\Search\DemandeRhSearch'
        ));
    }

}
