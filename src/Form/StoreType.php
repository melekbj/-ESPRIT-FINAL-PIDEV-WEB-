<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Store;
use App\Entity\CategorieStore;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class StoreType extends AbstractType
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', null, [
            'constraints' => [
                new NotBlank(),
            ],
        ])
        ->add('ville', ChoiceType::class, [
            'required' => true,
            'multiple' => false,
            'expanded' => false,
            'placeholder' => 'Choose state',
            'mapped' => false,
            'choices'  => [
                'Ariana' => 'Ariana',
                'Beja' => 'Beja',
                'Ben Arous' => 'Ben Arous',
                'Bizerte' => 'Bizerte',
                'Gabes' => 'Gabes',
                'Gafsa' => 'Gafsa',
                'Jendouba' => 'Jendouba',
                'Kairouan' => 'Kairouan',
                'Kasserine' => 'Kasserine',
                'Kebili' => 'Kebili',
                'Kef' => 'Kef',
                'Mahdia' => 'Mahdia',
                'Manouba' => 'Manouba',
                'Medenine' => 'Medenine',
                'Monastir' => 'Monastir',
                'Nabeul' => 'Nabeul',
                'Sfax' => 'Sfax',
                'Sidi Bouzid' => 'Sidi Bouzid',
                'Siliana' => 'Siliana',
                'Sousse' => 'Sousse',
                'Tataouine' => 'Tataouine',
                'Tozeur' => 'Tozeur',
                'Tunis' => 'Tunis',
                'Zaghouan' => 'Zaghouan',
            ],  
        ])
        ->add('adresse', TextType::class, [
            'mapped' => false, 
        ])               
        ->add('location', HiddenType::class)
        // ->add('photo', FileType::class, [
        //     'required' => false,
        //     'label' => 'Profile Picture',
        //     'data_class' => null,
        // ])
        ->add('imageFile', VichFileType::class, [
            'required' => true,
            'allow_delete' => false,
            'download_uri' => true,
        ])
        ->add('categorie', EntityType::class, [
            'class' => CategorieStore::class,
            'choice_label' => 'libelle',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $store = $event->getData();
            $imageFile = $store->getPhoto();
        
            if ($imageFile instanceof UploadedFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
        
                // Move the file to the directory where images are stored
                try {
                    $imageFile->move(
                        $this->params->get('kernel.project_dir') . '/public/uploads',
                        $newFilename
                    );                                 
                } catch (FileException $e) {
                    // Handle the exception
                }
        
                $store->setPhoto($newFilename);
            }
        
            $ville = $event->getForm()->get('ville')->getData();
            $adresse = $event->getForm()->get('adresse')->getData();
            $location = $ville . ', ' . $adresse;
            $store->setLocation($location);
        });
    }        

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Store::class,
        ]);
    }
}