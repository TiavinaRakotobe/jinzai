<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class DemandeHistoryValidationType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /* ->add('demandeConge', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
              'class' => 'AppBundle:DemandeConge',
              'property' => 'numero',
              'expanded' => false,
              'multiple' => false,
              'label' => 'Demande congé',
              'required' => true,
              'attr' => array(
              'title' => "Demande congé"
              ))
              )
              ->add('modeleWorkflowStep', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
              'class' => 'AppBundle:ModeleWorkflowSteps',
              'query_builder' => function (EntityRepository $er) {
              return $er->createQueryBuilder('WE')
              ->where('WE.enabled = 1');
              },
              'property' => 'libelle',
              'expanded' => false,
              'multiple' => false,
              'label' => 'Workflow étape',
              'required' => true,
              'attr' => array(
              'title' => "Workflow étape"
              ))
              )
              ->add('validator', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
              'class' => 'AdminUserBundle:Utilisateur',
              'query_builder' => function (EntityRepository $er) {
              return $er->createQueryBuilder('U')
              ->where('U.enabled = 1');
              },
              'property' => 'name',
              'expanded' => false,
              'multiple' => false,
              'label' => 'Validateur',
              'required' => true,
              'attr' => array(
              'title' => "Validateur"
              ))
              ) */
            ->add('commentaire', null, array(
                'attr' => array(
                    'class' => 'form-control'
                ),
                'label' => 'Commentaire',
            ))
            ->add('validationStatus', 'hidden', array())
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\DemandeHistoryValidation'
        ));
    }

}
