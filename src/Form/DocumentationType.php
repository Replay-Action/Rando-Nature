<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Documentation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class DocumentationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('auteur',HiddenType::class)
            ->add('titre', TextType::class)
            ->add('intro', TextareaType::class)
            ->add('categorie', EntityType::class,[
                'class' => Categorie::class,

            ])
            ->add('paragraphe1', TextareaType::class)
            ->add('paragraphe2', TextareaType::class,[
                'required' => false,
            ])
            ->add('url',TextType::class,[
                'required' => false,
            ])
            ->add('image', FileType::class, [
                'label' => 'Choisissez votre fichier',
                'required' => false,
                'data_class' => null,
                'constraints' => [
                    new File([
                        'maxSize' => '1200k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                            'image/jpeg',
                            'image/png',
                            'image/svg+xml',
                        ],
                        'mimeTypesMessage' => 'Le logo doit être de type .png, .svg ou .pdf',
                    ])
                ]
            ])
            ->add('imageLegende', TextType::class,[
                'required' => false,
            ])
            ->add('image2', FileType::class, [
                'label' => 'Choisissez votre fichier',
                'required' => false,
                'data_class' => null,
                'constraints' => [
                    new File([
                        'maxSize' => '1200k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                            'image/jpeg',
                            'image/png',
                            'image/svg+xml',
                        ],
                        'mimeTypesMessage' => 'Le logo doit être de type .png, .svg ou .pdf',
                    ])
                ]
            ])
            ->add('imageLegende2', TextType::class,[
                'required' => false,
            ])
            ->add('valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Documentation::class,
        ]);
    }
}
