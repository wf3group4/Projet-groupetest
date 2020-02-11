<?php

namespace App\Form;

use App\Entity\Portfolio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PortfolioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('img_url', FileType::class,  [
                'label' => 'Ajouter une image', 
                'required' => false, 
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' =>'2000k', 
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'application/pdf'
                        ],
                        'mimeTypesMessage' => 'Le fichier doit être au format png,jpeg ou pdf',
                    ])
                ]
            ])
            ->add('liens', TextType::class, [
                'label' => 'Ajouter un lien',
                'required' => false
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
            'data_class' => Portfolio::class,
        ]);
    }
}
