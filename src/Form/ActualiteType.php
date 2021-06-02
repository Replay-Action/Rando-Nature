<?php


namespace App\Form;


use App\Entity\Actualite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActualiteType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('actu', TextType::class
            )
            ->add('date_actu', DateTimeType::class,[
                'widget' => 'single_text'
            ])
            ->add('valider', SubmitType::class)
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Actualite::class,
        ]);
    }
}