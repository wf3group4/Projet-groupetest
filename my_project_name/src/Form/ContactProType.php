<?php

namespace App\Form;

use App\Controller\ListeController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ContactProType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'mapped' => false,
                'label' => 'Nom',
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'mapped' => false,
            ])
            ->add('sujet', TextType::class, [
                'label' => 'Objet',
                'mapped' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'mapped' => false,
            ] )
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'mapped' => false,
                'attr' => [
                    'rows' => 10]
            ])
            ->add('Envoyer_le_message', SubmitType::class,
                [
                    'attr' => [
                        'class' => 'text-right',
                    ],
                ])
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ListeController::class,
        ]);
    }
}
