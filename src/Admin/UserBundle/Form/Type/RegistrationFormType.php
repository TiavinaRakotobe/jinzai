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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class RegistrationFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, array(
                'label' => 'Nom',
                'attr' => array(
                    'class' => 'form-control'
                ),
                'required' => true
            ))
            ->add('matricule', null, array(
                'label' => 'Matricule',
                'attr' => array(
                    'class' => 'form-control'
                ),
                'required' => true
            ))
            ->add('username', null, array(
                'label' => 'Pseudo',
                'attr' => array(
                    'class' => 'form-control'
                ),
                'required' => true
            ))
            ->add('direction', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Direction',
                'required' => true,
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
            ->add('expat', null, array(
                'attr' => array(
                    'class' => 'checkbox icheck'
                )
            ))
            ->add('email', 'email', array(
                'label' => 'form.email',
                'translation_domain' => 'FOSUserBundle',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('manager', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Manager',
                'required' => false,
                'class' => 'Admin\UserBundle\Entity\Utilisateur',
                'property' => 'name',
                'attr' => array(
                    'class' => 'form-control select2'
                )
            ))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array(
                    'label' => 'Mot de passe',
                    'attr' => array(
                        'class' => 'form-control'
                    )
                ),
                'second_options' => array(
                    'label' => 'Confirmer le mot de passe',
                    'attr' => array(
                        'class' => 'form-control'
                    )
                ),
                'invalid_message' => 'Les deux mots de passe ne sont pas identiques',
                'required' => true
            ))
            ->add('roles', 'choice', array('choices' =>
                array(
                    'ROLE_USER' => 'Utilisateur',
                    'ROLE_ADMIN' => 'Administrateur',
                    'ROLE_ASSISTANT' => 'Assistant(e) de direction'
                ),
                'label' => 'Rôle',
                'expanded' => true,
                'multiple' => true,
                'attr' => array(
                    'class' => 'icheck',
                    'title' => "Role"
                )
                )
            )
        /* ->add('photo', 'Symfony\Component\Form\Extension\Core\Type\FileType', array(
          'label' => 'Photo',
          'required' => false,
          'attr' => array(
          'class' => 'input-file'
          )
          )) */
        ;
        //->add('manager','fos_user_username');
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'app_user_registration';
    }

}
