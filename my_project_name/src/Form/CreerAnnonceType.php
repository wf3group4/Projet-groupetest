<?php

namespace App\Form;

use App\Entity\Annonces;
use App\Repository\TagsRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class CreerAnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class)
            // ->add('description', TextareaType::class, [
            //     'label' => "Ajoutez le texte de votre annonce",
            //     'attr' => [
            //         'rows' => 10,
            //     ],
            //     ])
            ->add('description', CKEditorType::class, array(
                'config' => array(
                    'uiColor' => '#52b8df',
                    'toolbar' => 'full',
                    'required' => true,
                    'language' => 'fr',
                ),
            ))
            ->add('prix', MoneyType::class, [
                'label' => 'Prix',
                'invalid_message' => 'Vous devez entrer un nombre',
            ])
            ->add('tag', EntityType::class, [
                'class' => 'App:Tags',
                'choice_label' => 'nom',
                'label'     => 'Quels tags souhaitez vous ajouter ?',
                'expanded'  => true,
                'multiple'  => true,
            ])
            ->add('date_limite', DateTimeType::class)
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'row_attr' => [
                    'class' => 'text-right'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Annonces::class,
        ]);
    }
}
