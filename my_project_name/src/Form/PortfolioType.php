<?php

namespace App\Form;

use App\Entity\Portfolio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PortfolioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('img_url', FileType::class, [
                'label' => 'Ajouter une image', 
                'required' => false, 
                'constraints' => [
                    new File([
                        'maxSize' =>'2000k', 
                        'mimeTypes' => [
                            'image/png',
                            'jpeg'
                        ],
                        'mimeTypesMessage' => 'Le fichier doit être au format png ou jpeg',
                    ])
                ]
            ])
            ->add('liens', TextType::class, [
                'label' => 'Ajouter un lien',
                'required' => false
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Portfolio::class,
        ]);
    }
}
