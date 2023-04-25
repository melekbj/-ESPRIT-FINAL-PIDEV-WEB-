<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Evenement;
use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date')
            ->add('nbPlaces')
            // ->add('user', EntityType::class
            //     , [
            //         'class' => User::class,
            //         'choice_label' => 'nom',
            //         'label' => 'User',
            //         'placeholder' => 'Choose a user',
            //         'required' => true,

            //     ])
                // ->add('event', EntityType::class
                // , [
                //     'class' => Evenement::class,
                //     'choice_label' => 'titreEv',
                //     'label' => 'Event',
                //     'placeholder' => 'Choose an event',
                //     'required' => true,

                // ])

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
