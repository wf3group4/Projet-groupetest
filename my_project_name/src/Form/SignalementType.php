<?php

namespace App\Form;

use App\Entity\Signalement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SignalementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('message', TextareaType::class, [
            'label' => "Explique nous les raisons de ton signalement:",
            'attr' => [
                'rows' => 10,
            ],
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
            'data_class' => Signalement::class,
        ]);
    }
}
