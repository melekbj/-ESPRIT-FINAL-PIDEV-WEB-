<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\EventType;
use Symfony\Component\Form\AbstractType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


class FormEvent extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titreEv')
            ->add('DescEv')
            ->add('date_debut', DateType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Type(\DateTime::class),
                    new LessThanOrEqual([
                        'propertyPath' => 'parent.all[date_fin].data',
                        'message' => 'The start date must be before the end date.',
                    ]),
                    new Callback(function ($data, ExecutionContextInterface $context) {
                        if ($data && $data->format('Y') !== '2023') {
                            $context->buildViolation('The start date must start with the year 2023.')
                                ->atPath('date_debut')
                                ->addViolation();
                        }
                    }),
                ],
            ])
            ->add('date_fin', DateType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Type(\DateTime::class),
                    new Callback(function ($data, ExecutionContextInterface $context) {
                        if ($data && $data->format('Y') !== '2023') {
                            $context->buildViolation('The end date must start with the year 2023.')
                                ->atPath('date_fin')
                                ->addViolation();
                        }
                    }),
                ],
            ])
            // ->add('imageEv')
            ->add('imageFile', VichFileType::class, [
                'required' => true,
                'allow_delete' => true,
                'download_uri' => true,
            ])
            ->add('lieuEv')
            ->add('nbMax', IntegerType::class, [
                'constraints' => [
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'Please enter a number greater than zero for the "nbMax" field.',
                    ]),
                ],
            ])
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
