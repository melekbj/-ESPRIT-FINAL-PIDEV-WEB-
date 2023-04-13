<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\Commande;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/client')]
class ClientController extends AbstractController
{
    
    #[Route('/', name: 'app_client_index')]
    public function index(): Response
    {
        $user = $this->getUser();
        // Get the image associated with the user
        $image = $user->getImage();
        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
            'image' => $image,
        ]);
    }
    

    #[Route('/mon_profil', name: 'app_client_profile')]
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
            return $this->redirectToRoute('app_client_profile');
            
        }
        // If the form was not submitted or is not valid, render the profile edit form
        return $this->render('client/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'image' => $image,
        ]);
    }

    #[Route('/updatePassword', name: 'update_password_client', methods:['POST'])] 
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
            return $this->redirectToRoute('app_client_profile');
        }
        
        // Check that the new password and confirm password match
        if ($newPassword !== $confirmPassword) {
            $this->addFlash('error', 'New password and confirm password do not match.');
            return $this->redirectToRoute('app_client_profile');
        }
        
        // Encode the new password and update the user's password
        $newEncodedPassword = $passwordEncoder->encodePassword($user, $newPassword);
        $user->setPassword($newEncodedPassword);
        $this->getDoctrine()->getManager()->flush();
        
        // Redirect to a success page
        $this->addFlash('success', 'Password updated successfully.');
        return $this->redirectToRoute('app_client_profile');
    }




}
