<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class AdhesionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class,[
                'required'=>true
            ])
            ->add('prenom', TextType::class,[
                'required'=>true
            ])
            ->add('pseudo',TextType::class,[
                'required'=>true
            ])
            ->add('email', EmailType::class, [
                'required'=>true
            ])
            ->add('date_de_naissance', BirthdayType::class, [
                'format' => 'ddMMyyyy',
                'required'=>true,
            ])
            ->add('telephone',IntegerType::class,[
                'required'=>true
            ])
            ->add('jAccepteLesTermesCi-dessous', CheckboxType::class, array(
                    'label'=> "J'accepte les termes ci-dessous",
                    'mapped' => false,
                    'constraints' => new IsTrue(),
                )
            )
            ->add('envoyer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
