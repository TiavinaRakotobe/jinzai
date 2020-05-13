<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use AppBundle\Constants\GestionCongeConstant;

class DemandeRHType extends AbstractType
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
                'required' => true,
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
                'property' => 'name',
                'expanded' => false,
                'multiple' => false,
                'label' => 'Demandeur',
                'required' => true,
                'attr' => array(
                    'title' => "Demandeur",
                    'class' => 'form-control select2'
                ))
            )
            /*->add('toDate', 'Symfony\Component\Form\Extension\Core\Type\DateType', array(
                'label' => 'Date de début',
                'attr' => array(
                    'class' => 'form-control datepicker date-range-start',
                    'placeholder' => 'Date de début'
                ),
                'required' => false,
                'widget' => 'single_text',
                'format' => 'y-MM-dd'
                )
            )*/
            ->add('fromDate', 'Symfony\Component\Form\Extension\Core\Type\DateType', array(
                'label' => 'Date de fin',
                'attr' => array(
                    'class' => 'form-control datepicker date-range-start',
                    'placeholder' => 'Date de livraison souhaité'
                ),
                'required' => false,
                'widget' => 'single_text',
                'format' => 'y-MM-dd'
                )
            )
            ->add('commentaire', 'textarea', array(
                'label' => 'Commentaire:',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                    'title' => "Description"
                )
                )
            )
			->add('nombreDemande', 'integer', array(
                'label' => 'Nombre ',
				'required' => true,
                'attr' => array(
                    'class' => 'form-control',
					'min' => 1
                )
            ))
            
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\DemandeRH'
        ));
    }

}
