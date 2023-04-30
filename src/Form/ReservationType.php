<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Evenement;
use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateTimeType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                // 'format' => 'dd-MM-yyyy    HH:mm:ss',
                'html5' => true,
                // 'data' => new \DateTime('now'),
            ])
            ->add('nbPlaces', IntegerType::class, [
                'constraints' => [
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'Please enter a number greater than zero for the "nbMax" field.',
                    ]),
                    new LessThanOrEqual([
                        'value' => 10,
                        'message' => 'The maximum number of places allowed is 10.',
                    ]),
                ],
            ])

            ->add('save', SubmitType::class, [
                'label' => 'Reserver',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
