<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifCompteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Name', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Prénom',],
            ])
            ->add('Lastname', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Nom',],
            ])
            ->add('Email', EmailType::class, [
                'attr' => ['placeholder' => 'Email',],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['placeholder' => 'Prénom',],
                'required' => false,
                'attr' => [
                    'rows' => 10
                ],
                'row_attr' => [
                    'mapped' => false,
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passes ne correspond pas.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => 'mot de passe',
                    ] ,
                ],
                'second_options' => [
                    'label' => 'Répéter le mot de passe',
                    'attr' => [
                        'placeholder' => 'Répéter le mot de passe',
                    ],
                    ],
            ])
            ->add('avatar',FileType::class,[
                'label' => 'L\'avatar',
                'mapped' => false,
                'required' => false,
                'block_name' => 'avatar',
            ])
            ->add('save', SubmitType::class,[
                'label' => 'Envoyer',
                'row_attr' => [
                    'class' => 'text-right'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
