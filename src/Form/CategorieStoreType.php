<?php

namespace App\Form;

use App\Entity\CategorieStore;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CategorieStoreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('libelle', null, [
            'constraints' => [
                new NotBlank(),
            ],
        ])
        
        ->add('save', SubmitType::class, [
            'label' => 'Confirm',
            'attr' => [
                'class' => 'btn btn-primary'
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CategorieStore::class,
        ]);
    }
}