<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use AppBundle\Constants\GestionCongeConstant;

class DemandeCongeType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->menuId = $options['menuId'];
        $this->userId = $options['userId'];

        $builder
            ->add('typeConge', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'class' => 'AppBundle:TypeConge',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('T')
                        ->where('T.enabled = 1 AND T.imputationType = ' . $this->menuId);
                },
                'property' => 'libelle',
                'expanded' => false,
                'multiple' => false,
                'label' => 'Type congé',
                'required' => true,
                'attr' => array(
                    'title' => "Type congé",
                    'class' => 'form-control'
                ))
            )
            ->add('dateStart', 'Symfony\Component\Form\Extension\Core\Type\DateType', array(
                'label' => 'Date début',
                'attr' => array(
                    'class' => 'form-control datepicker date-range-start',
                    'placeholder' => 'Date de début',
                    'autocomplete' => 'off'
                ),
                'widget' => 'single_text',
                'format' => 'y-MM-dd'
                )
            )
            ->add('dateStartMorning', null, array(
                'attr' => array(
                    'class' => 'checkbox',
                ),
                'label' => 'Matin'
            ))
            ->add('dateStartAfternoon', null, array(
                'attr' => array(
                    'class' => 'checkbox icheck_',
                ),
                'label' => 'Midi',
            ))
            ->add('dateEnd', 'Symfony\Component\Form\Extension\Core\Type\DateType', array(
                'label' => 'Date fin',
                'attr' => array(
                    'class' => 'form-control datepicker date-range-end',
                    'placeholder' => 'Date de fin',
                    'autocomplete' => 'off'
                ),
                'widget' => 'single_text',
                'format' => 'y-MM-dd'
                )
            )
            ->add('dateEndMorning', null, array(
                'attr' => array(
                    'class' => 'checkbox',
                ),
                'label' => 'Matin',
            ))
            ->add('dateEndAfternoon', null, array(
                'attr' => array(
                    'class' => 'checkbox icheck_',
                ),
                'label' => 'Midi',
            ))
            ->add('totalDays', null, array(
                'label' => 'Nombre de jour',
                'attr' => array(
                    'class' => 'form-control',
                    'readonly' => 'readonly'
                )
            ))
            ->add('dateRetour', 'Symfony\Component\Form\Extension\Core\Type\DateType', array(
                'label' => 'Date retour',
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Date retour',
                    'readonly' => 'readonly'
                ),
                'widget' => 'single_text',
                'format' => 'y-M-d'
                )
            )
            ->add('observations', null, array(
                'attr' => array(
                    'class' => 'form-control'
                ),
                'label' => 'Commentaire'
        ));
        if ($this->userId == 0) {
            $builder->add('demandor', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'class' => 'AdminUserBundle:Utilisateur',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('U')
                            ->where('U.enabled = 1');
                },
                'property' => 'name',
                'expanded' => false,
                'multiple' => false,
                'label' => 'DemandeurAd',
                'required' => true,
                'attr' => array(
                    'title' => "Demandeur",
                    'class' => 'form-control select2'
                ))
            );
        } else {
            $builder->add('demandor', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'class' => 'AdminUserBundle:Utilisateur',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('U')
                            ->where('U.enabled = 1 AND U.id=' . $this->userId);
                },
                'property' => 'name',
                'expanded' => false,
                'multiple' => false,
                'label' => 'Demandeur',
                'required' => true,
                'attr' => array(
                    'title' => "Demandeur",
                    'class' => 'form-control select2',
                    'readonly' => true
                ))
            );
        }

        $builder
            ->add('interim', 'collection', array(
                'type' => new InterimType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => true,
                'label' => false,
            ))
            ->add('document', 'collection', array(
                'type' => new DocumentType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => true,
                'label' => false,
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\DemandeConge',
            'menuId' => null,
            'userId' => null,
        ));
    }

}
