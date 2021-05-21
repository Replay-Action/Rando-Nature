<?php


namespace App\Form;


use App\Entity\Referent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReferentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nom');

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['date_class'=> Referent::class,
            ]);
    }
}