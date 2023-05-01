<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[^0-9]*$/',
                        'message' => 'Le champ "Nom" ne doit pas contenir de chiffres.',
                    ]),
                ],
            ])
            ->add('imageFile', VichFileType::class, [
                'required' => true,
                'allow_delete' => false,
                'download_uri' => true,
            ])
            ->add('prix', null, [
                'constraints' => [
                    new Positive([
                        'message' => 'Le prix doit être un nombre positif.',
                    ]),
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'Le prix doit être supérieur à zéro.',
                    ]),
                ],
            ])
            ->add('quantite', null, [
                'constraints' => [
                    new Positive([
                        'message' => 'La quantité doit être un nombre positif.',
                    ]),
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'La quantité doit être supérieure à zéro.',
                    ]),
                ],
            ])
            // ->add('etat')
            ->add('categorie',EntityType::class
               , [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'label' => 'Categorie Produit',
                'placeholder' => 'Choose a categorie',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
                
                ])
            // ->add('stores')
            ->add('save', SubmitType::class, [
                'label' => 'Create new +',
                'attr' => [
                    'class' => 'btn btn-info'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
