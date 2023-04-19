<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\Produit;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Vich\UploaderBundle\Handler\UploadHandler;




#[Route('/partner')]
class PartnerController extends AbstractController
{
    private UploadHandler $uploadHandler;

    public function __construct(UploadHandler $uploadHandler)
    {
        $this->uploadHandler = $uploadHandler;
    }

    
    #[Route('/', name: 'app_partner')]
    public function index(): Response
    {
        $user = $this->getUser();
        // Get the image associated with the user
        $image = $user->getImage();
        return $this->render('partner/index.html.twig', [
            'controller_name' => 'PartnerController',
            'image' => $image,
            'user' => $user
        ]);
    }

   

    #[Route('/profile', name: 'app_partner_profile')]
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
            // Remove the image file from the entity before persisting it to the database
            // $user->setImageFile(null);

            // Save the updated user information to the database
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirect to the user's profile page with a success message
            $this->addFlash('success', 'Profile updated successfully!');
            return $this->redirectToRoute('app_partner_profile');
            
        }
        // If the form was not submitted or is not valid, render the profile edit form
        return $this->render('partner/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'image' => $image,
        ]);
    }





  

  

   

   

    

    
    #[Route('/new', name: 'app_product_new')]
    public function new(Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        // Get the image associated with the user
        $image = $user->getImage();
        $produit = new Produit();
        $form = $this->createForm(ProductType::class, $produit);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $doctrine->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();
            $this->addFlash('success', 'Produit ajouté avec succès');
            return $this->redirectToRoute('app_product_new');
        }
        
        return $this->render('partner/addProduit.html.twig', [
            'form' =>$form->createView(),
            'image' => $image,
        ]);
    }
}
