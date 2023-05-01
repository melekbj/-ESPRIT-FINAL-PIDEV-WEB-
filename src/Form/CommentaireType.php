<?php

namespace App\Form;


use App\Entity\Commentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('contenu',TextareaType::class,['label'=>false,'attr'=>['placeholder'=>'Comment...','class'=>'stext-111 cl2 plh3 size-124 p-lr-18 p-tb-15']])
        ->add('save', SubmitType::class, [
            'label' => 'Add comment',
            'attr' => [
                'class' => 'btn btn-info'
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
        ]);
    }
}
