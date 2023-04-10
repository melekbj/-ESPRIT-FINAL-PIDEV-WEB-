<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Twilio\Rest\Client;
use App\Form\RegisterType;
use App\Service\SendSmsService;
use App\Service\SendMailService;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        // Get the current user
        $user = $this->getUser();
        
        // Get the image associated with the user
        $image = $user->getImage();
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'image' => $image,
        ]);
    }

    // #[Route('/profile', name: 'app_profile')]
    // public function indexProfile(): Response
    // {
    //     // Get the current user
    //     $user = $this->getUser();
        
    //     // Get the image associated with the user
    //     $image = $user->getImage();
        
    //     return $this->render('admin/profile.html.twig', [
    //         'controller_name' => 'AdminController',
    //         'image' => $image,
    //     ]);
    // }

    // #[Route('/profile', name: 'app_profile')]
    public function Baseindex(): Response
    {
        // Get the current user
        $user = $this->getUser();
        
        // Get the image associated with the user
        $image = $user->getImage();
        
        return $this->render('baseAdmin.html.twig', [
            'controller_name' => 'AdminController',
            'image' => $image,

        ]);
    }


    #[Route('/liste_des_utilisateurs', name: 'app_users')]
    public function ListeU(): Response
    {
        // Get the current user
        $user = $this->getUser();
        
        // Get the image associated with the user
        $image = $user->getImage();
        //recuperer le repository
        $repository = $this->getDoctrine()->getRepository(User::class);
        //utiliser findAll() pour recuperer toutes les classes
        $users = $repository->createQueryBuilder('u')
        ->where('u.roles LIKE :roles1 OR u.roles LIKE :roles2')
        ->andWhere('u.etat <> :etat')
        ->orderBy('u.nom', 'ASC') 
        ->setParameters([
            'roles1' => '%ROLE_CLIENT%',
            'roles2' => '%ROLE_PARTNER%',
            'etat' => 1
        ])
        ->getQuery()
        ->getResult();


        return $this->render('admin/ListeUsers.html.twig', [
            'users' => $users,
            'image' => $image,
        ]);
    }

    #[Route('/liste_des_partenaires', name: 'app_partners')]
    public function ListeP(): Response
    {
        // Get the current user
        $user = $this->getUser();
        
        // Get the image associated with the user
        $image = $user->getImage();
        //recuperer le repository
        $repository = $this->getDoctrine()->getRepository(User::class);
        //utiliser findAll() pour recuperer toutes les classes
        $users = $repository->findBy(['etat' => [1, -2]]);

        return $this->render('admin/ListePartners.html.twig', [
            'users' => $users,
            'image' => $image,
        ]);
    }

    #[Route('/deleteU/{id}', name: 'app_deleteU')]
    public function deleteU($id, UserRepository $rep, ManagerRegistry $doctrine ): Response
    {

        //recuperer la classe a supprimer
        $users = $rep->find($id);
        $rep=$doctrine->getManager();
        //supprimer la classe   
        $rep->remove($users);
        $rep->flush();
        //flash message
        $this->addFlash('success', 'User deleted successfully!');
        return $this->redirectToRoute('app_users'); 
        
    }


    #[Route('/updateU/{id}', name: 'app_updateU')]
    public function updateU($id, Request $request, UserRepository $rep, ManagerRegistry $doctrine): Response
    {
        // Get the current user
        $user = $this->getUser();
        
        // Get the image associated with the user
        $image = $user->getImage();
        // récupérer la classe à modifier
        $users = $rep->find($id);
        // créer un formulaire
        $form = $this->createForm(UserType::class, $users);
        // récupérer les données saisies
        $form->handleRequest($request);
        // vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // récupérer les données saisies
            $users = $form->getData();
            // persister les données
            $rep = $doctrine->getManager();
            $rep->persist($users);
            $rep->flush();
            //flash message
            $this->addFlash('success', 'User updated successfully!');
            return $this->redirectToRoute('app_users');
        }
        return $this->render('admin/EditUsers.html.twig', [
            'form' => $form->createView(),
            'image' => $image,
        ]);
    }

    


    // #[Route('/updateProfil', name: 'update_profile')]
    // public function updateProfile(Request $request)
    // {

    //     // retrieve form data
    //     $nom = $request->request->get('nom');
    //     $prenom = $request->request->get('prenom');
    //     $age = $request->request->get('age');
    //     $phone = $request->request->get('phone');
    //     $adresse = $request->request->get('adresse');
    //     $ville = $request->request->get('ville');
    //     // update user's information in the database
    //     $entityManager = $this->getDoctrine()->getManager();
    //     $user = $this->getUser(); 
    //     if($user){
    //         $user->setNom($nom);
    //         $user->setPrenom($prenom);
    //         $user->setAge($age);
    //         $user->setPhone($phone);
    //         $user->setAdresse($adresse);
    //         $user->setVille($ville);
    //         $entityManager->persist($user);
    //         $entityManager->flush();

    //         $request->getSession()->getFlashBag()->add('success', 'Profile updated successfully!');
    //         return $this->redirectToRoute('app_profile');
    //     }
        

    //     $request->getSession()->getFlashBag()->add('error', 'profile failed to update');
    //     return $this->redirectToRoute('app_profile');
    // }

    #[Route('/profile', name: 'app_profile')]
    public function updateProfile(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        // Get the image associated with the user
        $image = $user->getImage();

        // Create a new userType form and populate it with the user's data
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save the updated user information to the database
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirect to the user's profile page with a success message
            $this->addFlash('success', 'Profile updated successfully!');
            return $this->redirectToRoute('app_profile');
        }

        // If the form was not submitted or is not valid, render the profile edit form
        return $this->render('admin/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'image' => $image,
        ]);
    }

    
    #[Route('/updatePassword', name: 'update_password', methods:['POST'])] 
    public function updatePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // Get the current user
        $user = $this->getUser();
 
        // Get the submitted form data
        $actualPassword = $request->request->get('actualPassword');
        $newPassword = $request->request->get('newPassword');
        $confirmPassword = $request->request->get('ConfirmPassword');
        
        // Check that the current password is correct
        if (!$passwordEncoder->isPasswordValid($user, $actualPassword)) {
            $this->addFlash('error', 'Current password is incorrect.');
            return $this->redirectToRoute('app_profile');
        }
        
        // Check that the new password and confirm password match
        if ($newPassword !== $confirmPassword) {
            $this->addFlash('error', 'New password and confirm password do not match.');
            return $this->redirectToRoute('app_profile');
        }
        
        // Encode the new password and update the user's password
        $newEncodedPassword = $passwordEncoder->encodePassword($user, $newPassword);
        $user->setPassword($newEncodedPassword);
        $this->getDoctrine()->getManager()->flush();
        
        // Redirect to a success page
        $this->addFlash('success', 'Password updated successfully.');
        return $this->redirectToRoute('app_profile');
    }


    #[Route('/blockU/{id}', name: 'app_blockU')]
    public function blockU($id, UserRepository $rep, ManagerRegistry $doctrine,SendMailService $mail): Response
    {
        // Get the user to deactivate
        $user = $rep->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Set the user's etat to -1
        $user->setEtat(-1);

        $em = $doctrine->getManager();
        $em->persist($user);
        $em->flush();

        // Envoi du mail
        $mail->sendMail(
            'melekbejaoui29@gmail.com', 'Storeship',
            $user->getEmail(),
            'Account Restriction',
            'block',
        );

        //flash message
        $this->addFlash('success', 'User blocked successfully!');

        return $this->redirectToRoute('app_users');
    }


    #[Route('/approveU/{id}', name: 'app_approveU')]
    public function approveU($id, UserRepository $rep, ManagerRegistry $doctrine, SendMailService $mail): Response
    {
        // Get the user to deactivate
        $user = $rep->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Set the user's etat to -1
        $user->setEtat(0);



        $em = $doctrine->getManager();
        $em->persist($user);
        $em->flush();
        
        // Envoi du mail
        $mail->sendMail(
            'melekbejaoui29@gmail.com', 'Storeship',
            $user->getEmail(),
            'Account Approval Confirmation',
            'approve',
            [
                'user' => $user,
            ]
        );

        // $accountSid = 'ACa141e95e70a02a529768fb9df90ebcea';
        // $authToken = '5b7aa8ad0221159b8c404931b54487ef';
        // $fromNumber = '+15076288954';
    
        // // Instantiate the Twilio client
        // $twilio = new Client($accountSid, $authToken);
    
        // // Instantiate the SendSmsService and set the required parameters
        // $sms = new SendSmsService();
        // $sms->setAccountSid($accountSid);
        // $sms->setAuthToken($authToken);
        // $sms->setFromNumber($fromNumber);
        // $sms->setClient($twilio);
    
        // // Send an SMS to the user
        // $sms->send($user->getPhone(), 'Your account has been approved.');

        //flash message
        $this->addFlash('success', 'User approved successfully!');

        return $this->redirectToRoute('app_users');
    }

    #[Route('/disapproveU/{id}', name: 'app_disapproveU')]
    public function disapproveU($id, UserRepository $rep, ManagerRegistry $doctrine, SendMailService $mail): Response
    {
        // Get the user to deactivate
        $user = $rep->find($id);
    
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
    
        // Set the user's etat to -2
        $user->setEtat(-2);
    
        $em = $doctrine->getManager();
        $em->persist($user);
        $em->flush();
    
        // Envoi du mail
        $mail->sendMail(
            'melekbejaoui29@gmail.com', 'Storeship',
            $user->getEmail(),
            'Account Disapproval Notice',
            'disapprove',
            [
                'user' => $user,
            ]
        );

        // $accountSid = 'ACa141e95e70a02a529768fb9df90ebcea';
        // $authToken = '5b7aa8ad0221159b8c404931b54487ef';
        // $fromNumber = '+15076288954';
    
        // // Instantiate the Twilio client
        // $twilio = new Client($accountSid, $authToken);
    
        // // Instantiate the SendSmsService and set the required parameters
        // $sms = new SendSmsService();
        // $sms->setAccountSid($accountSid);
        // $sms->setAuthToken($authToken);
        // $sms->setFromNumber($fromNumber);
        // $sms->setClient($twilio);
    
        // // Send an SMS to the user
        // $sms->send($user->getPhone(), 'Your account has been disapproved.');
    
        //flash message
        $this->addFlash('error', 'User Disapproved !');
    
        return $this->redirectToRoute('app_partners');
    }
    



}
