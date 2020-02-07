<?php

namespace App\Form;

use App\Entity\ContactPro;
use Symfony\Component\Form\AbstractType;
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
            'label' => 'Nom'
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('sujet', TextType::class, [
                'label' => 'Objet', 
                'data' => 'Demande d\'information'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email'
            ] )
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'attr' => [
                    'rows' => 10]
            ])
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactPro::class,
        ]);
    }
}
