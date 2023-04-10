<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
// use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;  
use Symfony\Component\HttpFoundation\File\Exception\FileException;



class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roles', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'  => [
                    'Client' => 'ROLE_CLIENT',
                    'Partner' => 'ROLE_PARTNER',
                    'Admin' => 'ROLE_ADMIN',
                ],
            ])
            ->add('nom')
            ->add('prenom')
            ->add('age')
            ->add('genre', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'  => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                    'Autre' => 'Autre',
                ],
            ])
            ->add('ville', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Choose state',
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
            ->add('adresse')
            // ->add('image', FileType::class, [
            //     'required' => true,
            //     'label' => 'Profile Picture',
                
            // ])
            ->add('imageFile', VichFileType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_uri' => true,
            ])
            ->add('phone', TelType::class, [
                'attr' => [
                    'pattern' => '^\+?\d*$',
                    'placeholder' => '(+XXX)XXXXXXXXX'
                ]
            ])                       
            ->add('email', EmailType::class)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirm Password'],
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'The password must be at least {{ limit }} characters long.',
                        // add other options for the constraint if needed
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Register',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);

        // $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
        //     $user = $event->getData();
        //     $imageFile = $user->getImageFile();

        //     if ($imageFile instanceof UploadedFile) {
        //         $newFilename = uniqid().'.'.$imageFile->guessExtension();

        //         // Move the file to the directory where images are stored
        //         try {
        //             $imageFile->move(
        //                 $this->getParameter('images_directory'),
        //                 $newFilename
        //             );
        //         } catch (FileException $e) {
        //             // Handle the exception
        //         }

        //         $user->setImageFile($newFilename);
        //     }
        // });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}







     // Data transformer
        //  $builder->get('roles')
        //  ->addModelTransformer(new CallbackTransformer(
        //      function ($rolesArray) {
        //          // transform the array to a string
        //          return count($rolesArray)? $rolesArray[0]: null;
        //      },
        //      function ($rolesString) {
        //          // transform the string back to an array
        //          return [$rolesString];
        //     }
        //     ));