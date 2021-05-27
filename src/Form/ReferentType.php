<?php


namespace App\Form;


use App\Entity\Referent;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReferentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom');
        /**
         *   ->add('user',EntityType::class,[
        'class'=>User::class,
        ] )*/
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['date_class'=> Referent::class,
        ]);
    }
}