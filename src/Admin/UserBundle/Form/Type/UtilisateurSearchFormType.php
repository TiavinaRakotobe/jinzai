<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Admin\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class UtilisateurSearchFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('name', null, array(
                'label' => 'Nom',
                'attr' => array(
                    'class' => 'form-control'
                ),
                'required' => false
            ))
            ->add('matricule', null, array(
                'label' => 'Matricule',
                'attr' => array(
                    'class' => 'form-control'
                ),
                'required' => false
            ))
            ->add('username', null, array(
                'label' => 'Pseudo',
                'attr' => array(
                    'class' => 'form-control'
                ),
                'required' => false
            ))
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
                    'class' => 'form-control select2'
                )
            ))            
            
        ;        
    }
    	
	/**
     * @return string
     */
    public function getName()
    {
        return 'app_user_search';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Form\Search\UtilisateurSearch'
        ));
    }

}
