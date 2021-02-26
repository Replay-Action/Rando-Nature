<?php

namespace App\Form;

use App\Entity\Activite;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Sodium\add;

class ActiviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', ChoiceType::class, [
                'choices' => [
                    'Rando Vélo' => 'Rando Vélo',
                    'Escapade Séjour Vélo' => 'Escapade Séjour Vélo',
                    'Réunion' => 'Réunion',
                    'Spectacle' => 'Spectacle',
                    'Formation' => 'Formation',
                    'Autre évènement' => 'Autre évènement',
                ],])
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
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Activite::class,
        ]);
    }
}
