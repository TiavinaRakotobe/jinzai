<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use AppBundle\Constants\GestionCongeConstant;

class DemandeCongeSearchType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero', null, array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Numéro de la demande'
                ),
                'required' => false
            ))
            ->add('matricule', null, array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Numéro matricule'
                ),
                'required' => false
            ))
            ->add('typeConge', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'class' => 'AppBundle:TypeConge',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('T')
                        ->where('T.enabled = 1');
                },
                'property' => 'libelle',
                'expanded' => false,
                'multiple' => false,
                'label' => 'Type congé',
                'required' => false,
                'attr' => array(
                    'title' => "Type congé",
                    'class' => 'form-control select2',
                    'placeholder' => 'Type de congé'
                ))
            )
            ->add('dateStart', 'Symfony\Component\Form\Extension\Core\Type\DateType', array(
                'label' => 'Date début',
                'attr' => array(
                    'class' => 'form-control datepicker date-range-start',
                    'placeholder' => 'Date de début'
                ),
                'required' => false,
                'widget' => 'single_text',
                'format' => 'y-MM-dd'
            ))
            ->add('dateEnd', 'Symfony\Component\Form\Extension\Core\Type\DateType', array(
                'label' => 'Date fin',
                'attr' => array(
                    'class' => 'form-control datepicker date-range-end',
                    'placeholder' => 'Date de fin'
                ),
                'required' => false,
                'widget' => 'single_text',
                'format' => 'y-MM-dd'
            ))
            ->add('demandor', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'class' => 'AdminUserBundle:Utilisateur',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('U')
                        ->where('U.enabled = 1');
                },
                'property' => 'name',
                'expanded' => false,
                'multiple' => false,
                'label' => 'Demandeur',
                'required' => false,
                'attr' => array(
                    'title' => "Demandeur",
                    'class' => 'form-control select2',
                    'placeholder' => 'Demandeur'
                ))
            )
            ->add('direction', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Direction',
                'required' => false,
                'class' => 'AppBundle\Entity\Direction',
                'property' => 'libelle',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('D')
                        ->where('D.enabled = 1');
                },
                'attr' => array(
                    'title' => "Direction",
                    'class' => 'form-control select2',
                    'placeholder' => 'Direction'
            )))
            ->add('initiator', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'class' => 'AdminUserBundle:Utilisateur',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('U')
                        ->where('U.enabled = 1');
                },
                'property' => 'name',
                'expanded' => false,
                'multiple' => false,
                'label' => 'Initiateur',
                'required' => false,
                'attr' => array(
                    'title' => "Initiateur",
                    'class' => 'form-control select2',
                    'placeholder' => 'Initiateur'
                ))
            )
            ->add('demandeStatus', 'choice', array(
                'choices' => array(
                    GestionCongeConstant::CONGE_STATUS_INPROGRESS => GestionCongeConstant::CONGE_STATUS_INPROGRESS_LABEL,										
                    GestionCongeConstant::CONGE_STATUS_VALIDATED => GestionCongeConstant::CONGE_STATUS_VALIDATED_LABEL,
					GestionCongeConstant::CONGE_STATUS_CANCELLED => GestionCongeConstant::CONGE_STATUS_CANCELLED_LABEL,                    
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
                )
            )
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'demande_conge_search';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Form\Search\DemandeCongeSearch'
        ));
    }

}
