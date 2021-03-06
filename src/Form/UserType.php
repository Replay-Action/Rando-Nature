<?php

namespace App\Form;

use App\Entity\Photo;
use App\Entity\User;
use Doctrine\DBAL\Types\JsonType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\CallbackTransformer;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use function Sodium\add;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')

            # j'enleve role du formulaire  Il  doit etre en json et je l'ai ecrit en dur dans le UserController
            #  ->add('roles')
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'saisissez un mot de passe',
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'votre mot de passe doit contenir au moins 8 caracteres',
                            'max' => 4096,
                        ]),
                    ],
                    'label' => 'Votre mot de passe doit faire au moins 8 caracteres',
                ],
                'second_options' => ['label' => 'Confirmation : '],
            ])
            ->add('nom')
            ->add('prenom')
            ->add('telephone', IntegerType::class,[
            'required' => true])
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    'Adh??rent' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
            ])



            #class birthday pour que les ann??es soient dispos jusque 1901#
            ->add('date_naissance', BirthdayType::class, [
                # 'placeholder'=>'selectionner une valeur',
                'format' => 'ddMMyyyy'
            ])
            ->add('photos', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ]);




        // Data transformer, pour gerer les r??les
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return count($rolesArray) ? $rolesArray[0] : null;

                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];


                }
            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
