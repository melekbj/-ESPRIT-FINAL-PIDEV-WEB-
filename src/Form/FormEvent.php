<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\EventType;
use Symfony\Component\Form\AbstractType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class FormEvent extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titreEv')
            ->add('DescEv')
            ->add('date_debut', null, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('date_fin', null, [
                'constraints' => [
                    new NotBlank(),
                    new GreaterThan([
                        'propertyPath' => 'parent.all[date_debut].data',
                        'message' => 'The date_fin should be greater than the date_debut.'
                    ]),
                ],
            ])
            // ->add('imageEv')
            ->add('imageFile', VichFileType::class, [
                'required' => true,
                'allow_delete' => true,
                'download_uri' => true,
            ])
            ->add('lieuEv')
            ->add('nbMax')
            ->add('type',EntityType::class
               , [
                 'class' => EventType::class,
                 'choice_label' => 'libelle',
                'label' => 'Evenement Type',
                    'placeholder' => 'Choose a type',
                    'required' => true,
                
                ])
                ->add('save', SubmitType::class, [
                    'label' => 'Create new',
                    'attr' => [
                        'class' => 'btn btn-primary'
                    ]
                ]);
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
