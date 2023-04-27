<?php
namespace App\Form;

use App\Entity\Reclamation;
use App\Entity\TypeReclamation;
use Symfony\Component\Form\AbstractType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', null, [
                'label' => 'Description',
            ])
            ->add('type', EntityType::class, [
                'class' => TypeReclamation::class,
                'choice_label' => 'nom', // the property to use as the option label
                'label' => 'Type',
                'expanded' => false,
            ])
            ->add('imageFile', VichFileType::class, [
                'required' => true,
                'allow_delete' => true,
                'download_uri' => true,
            ])

            // ->add('image', FileType::class, [
            //     'label' => 'Image (JPEG, PNG, or GIF file)',
            //     'required' => false,
            // ]);
            ;
             
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}


