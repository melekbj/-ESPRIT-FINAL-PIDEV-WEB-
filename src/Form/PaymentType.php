<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {


        $builder
        ->add('cardNumber', TextType::class, [
            'label' => 'Card Number',
            'constraints' => [
                new Length([
                    'min' => 16,
                    'max' => 16,
                    'minMessage' => 'Card number must be 16 digits',
                    'maxMessage' => 'Card number must be 16 digits'
                ]),
            ],
            'required' => true,
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Enter card number',
            ]
        ])
        ->add('expirationMonth', TextType::class, [
            'label' => 'Expiration Month',
            'constraints' => [
                new Regex([
                    'pattern' => '/^(0?[1-9]|1[0-2])$/',
                    'message' => 'Expiration month must be between 01-12'
                ]),
            ],
            'required' => true,
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Enter expiration month',
            ]
        ])
        ->add('expirationYear', TextType::class, [
            'label' => 'Expiration Year',
            'constraints' => [
                new GreaterThanOrEqual([
                    'value' => date('Y'),
                    'message' => 'Expiration year must be equal or higher than the current year'
                ])
            ],
            'required' => true,
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Enter expiration year',
            ]
        ])
        ->add('cvc', TextType::class, [
            'label' => 'Secret Code',
            'constraints' => [
                new Length([
                    'min' => 3,
                    'max' => 3,
                    'minMessage' => 'Secret code must be 3 digits',
                    'maxMessage' => 'Secret code must be 3 digits'
                ]),
            ],
            'required' => true,
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Enter secret code',
            ]
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Submit',
            'attr' => [
                'class' => 'btn btn-primary'
            ]
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
