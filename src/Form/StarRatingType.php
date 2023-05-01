<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StarRatingType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                '1 star' => 1,
                '2 stars' => 2,
                '3 stars' => 3,
                '4 stars' => 4,
                '5 stars' => 5,
            ],
            'expanded' => true,
            'multiple' => false,
            'label_attr' => [
                'class' => 'rating-label'
            ],
            'attr' => [
                'class' => 'star-rating'
            ],
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
