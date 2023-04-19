<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\EventType;
use Symfony\Component\Form\AbstractType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class FormEvent extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titreEv')
            ->add('DescEv')
            ->add('date_debut')
            ->add('date_fin')
            // ->add('imageEv')
            ->add('imageFile', VichFileType::class, [
                'required' => true,
                'allow_delete' => true,
                'download_uri' => true,
            ])
            ->add('lieuEv')
            ->add('nbMax')
            // ->add('type')
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
