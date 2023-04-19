<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('password', PasswordType::class, [
            //     'label' => 'Entrez votre nouveau mot de passe',
            //     'attr' => [
            //         'class' => 'form-control'
            //     ]
            // ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'first_options'  => ['label' => 'Nouveau Password'],
                'second_options' => ['label' => 'Confirm Password'],
                // 'constraints' => [
                //     new Length([
                //         'min' => 8,
                //         'minMessage' => 'The password must be at least {{ limit }} characters long.',
                //         // add other options for the constraint if needed
                //     ]),
                // ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
