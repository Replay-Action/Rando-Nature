<?php

namespace App\Form;

use App\Entity\Activite;
use App\Entity\Etat;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ActiviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class/**ChoiceType::class, [
            'choices' => [
            'Rando Vélo' => 'Rando Vélo',
            'Escapade Séjour Vélo' => 'Escapade Séjour Vélo',
            'Réunion' => 'Réunion',
            'Spectacle' => 'Spectacle',
            'Formation' => 'Formation',
            'Autre évènement' => 'Autre évènement',
            ],]**/)
            ->add('date_activite')
            ->add('duree')
            ->add('distance')
            ->add('infos_activite', TextareaType::class)
            ->add('denivele')
            ->add('difficulte', ChoiceType::class, [
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    "3" => 3,

                ],])
            ->add('etat', EntityType::class, [
                'class' => Etat::class,
                'label' => 'etat',
                'disabled' => true,
                'choice_label' => function ($etat) {
                    return $etat->getLibelle();
                }
            ])
            ->add('lieu', LieuType::class, [

            ])
            ->add('docPdfs', FileType::class,[
                //  'data_class'=> null,
                'label' => false,
                'mapped' => false,
                'required' => false
            ])
            ->add('categorie',ChoiceType::class,[
                'choices' => [
                    'Balade du dimanche'=>'Balade du dimanche',
                    'Escapade'=>'Escapade',
                    'Formation mécanique'=>'Formation mécanique',
                    'Formation Sécurité'=>'Formation Sécurité',
                    'Formation Secourisme'=>'Formation Secourisme',
                    'Formation photo et vidéo'=>'Formation photo et vidéo',
                    'Film Documentaire'=>'Film Documentaire',
                    'Ecocitoyenneté'=>'Ecocitoyenneté',
                    'Longe côte'=>'Longe côte',
                    'Réunion'=>'Réunion',

                ]]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Activite::class,
        ]);
    }
}
