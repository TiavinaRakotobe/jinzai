<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class ModeleWorkflowStepsValidatorType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /* ->add('modeleWorkflowStepsId', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
              'class' => 'AppBundle:ModeleWorkflowSteps',
              'query_builder' => function (EntityRepository $er) {
              return $er->createQueryBuilder('Wfs')
              ->where('Wfs.enabled = 1');
              },
              'property' => 'libelle',
              'expanded' => false,
              'multiple' => false,
              'label' => 'Etape',
              'required' => true,
              'attr' => array(
              'title' => "Etape"
              ))
              ) */
            ->add('validatorId', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
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
                    'title' => "Validateur",
					'class' => 'form-control select2'
                ))
            )
        //->add('enabled')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ModeleWorkflowStepsValidator'
        ));
    }

}
