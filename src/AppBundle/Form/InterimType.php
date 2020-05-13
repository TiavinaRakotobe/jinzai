<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use AppBundle\Constants\GestionCongeConstant;

class InterimType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /* ->add('utilisateur', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
              'class' => 'AdminUserBundle:Utilisateur',
              'query_builder' => function (EntityRepository $er) {
              return $er->createQueryBuilder('U')
              ->where('U.enabled = 1');
              },
              'property' => 'name',
              'expanded' => false,
              'multiple' => false,
              'label' => 'Utilisateur',
              'required' => true,
              'attr' => array(
              'title' => "Utilisateur",
              'class' => 'form-control'
              ))
              ) */
            ->add('interim', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'class' => 'AdminUserBundle:Utilisateur',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('U')
                        ->where('U.enabled = 1');
                },
                'property' => 'name',
                'expanded' => false,
                'multiple' => false,
                'label' => 'Interim',
                'required' => true,
                'attr' => array(
                    'title' => "Interim",
                    'class' => 'form-control select2'
                ))
            )
            ->add('fromDate', 'Symfony\Component\Form\Extension\Core\Type\DateType', array(
                'label' => 'Date début',
                'attr' => array(
                    'class' => 'form-control datepicker date-range-start',
                    'placeholder' => 'Date de début'
                ),
                'widget' => 'single_text',
                'format' => 'y-MM-dd'
                )
            )
            ->add('toDate', 'Symfony\Component\Form\Extension\Core\Type\DateType', array(
                'label' => 'Date fin',
                'attr' => array(
                    'class' => 'form-control datepicker date-range-end',
                    'placeholder' => 'Date de fin'
                ),
                'widget' => 'single_text',
                'format' => 'y-MM-dd'
                )
            )
            ->add('interimType', 'choice', array(
                'choices' => array(
                    GestionCongeConstant::INTERIM_TYPE_FONCTION => GestionCongeConstant::INTERIM_TYPE_FONCTION_LABEL,
                    GestionCongeConstant::INTERIM_TYPE_VALIDATION => GestionCongeConstant::INTERIM_TYPE_VALIDATION_LABEL
                ),
                'label' => 'Interim type',
                'expanded' => false,
                'attr' => array(
                    'class' => 'form-control',
                    'title' => "Interim type"
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
            'data_class' => 'AppBundle\Entity\Interim'
        ));
    }

}
