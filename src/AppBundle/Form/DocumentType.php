<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fichier', 'file', array(
                'attr' => array(
                    'title' => "Pièce justificative",
                    'class' => 'filestyle',
                    'data-iconName' => 'fa fa-upload',
                    'data-placeholder' => 'Aucun fichier selectionné',
                    'data-buttonText' => 'Parcourir',
                ))
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Document'
        ));
    }

}
