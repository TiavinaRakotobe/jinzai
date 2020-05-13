<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class ModeleWorkflowType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', 'text', array(
                'label' => 'Libellé',
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                    'title' => "Libellé")))
            ->add('direction', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'class' => 'AppBundle:Direction',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('D')
                        ->where('D.enabled = 1');
                },
                'property' => 'sigle',
                'expanded' => true,
                'multiple' => true,
                'label' => 'Directions',
                'required' => true,
                'attr' => array(
                    'title' => "Direction",
                    'class' => 'icheck'
                ))
            )
            ->add('description', 'textarea', array(
                'label' => 'Description:',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                    'title' => "Description"))
            )
            ->add('enabled', 'checkbox', array(
                'label' => 'Activé',
                'attr' => array(
                    'class' => 'checkbox icheck'
                ),
                'required' => false
            ))
            ->add('modeleWorkflowSteps', 'collection', array(
                'type' => new ModeleWorkflowStepsType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => true,
                'label' => false,
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ModeleWorkflow',
            'csrf_protection' => false
        ));
    }

}
