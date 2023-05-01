<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Store;
use App\Form\UserType;
use App\Entity\Produit;
use App\Form\StoreType;
use Twilio\Rest\Client;
use App\Form\ProductType;
use App\Entity\CategorieStore;
use App\Entity\DetailCommande;
use App\Service\SendSmsService;
use App\Repository\StoreRepository;
use App\Repository\RatingRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CategorieStoreRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Handler\UploadHandler;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;





#[Route('/partner')]
class PartnerController extends AbstractController
{
    // private UploadHandler $uploadHandler;

    // public function __construct(UploadHandler $uploadHandler)
    // {
    //     $this->uploadHandler = $uploadHandler;
    // }

    
    // #[Route('/', name: 'app_partner')]
    // public function index(): Response
    // {
    //     $user = $this->getUser();
    //     // Get the image associated with the user
    //     $image = $user->getImage();
    //     return $this->render('partner/index.html.twig', [
    //         'controller_name' => 'PartnerController',
    //         'image' => $image,
    //         'user' => $user
    //     ]);
    // }

   

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

// .........................................Gestion categorie Store..........................................................


    

   
// .........................................Gestion Store..........................................................
   
    #[Route('/', name: 'app_partner')]
    public function index(StoreRepository $storeRepository, Security $security): Response
    {
            
            $user = $security->getUser();
            $store = $storeRepository->findStoreByUserId($user->getId());

            if ($store !== null) {
                return $this->redirectToRoute('app_store_show_partner', [
                    'id' => $store->getId(),
                    'user' => $user,
                    // 'image' => $image
                ]);
            } else {
                // Handle the case where the user doesn't have a store
                // For example, you could redirect them to the new store page
                return $this->redirectToRoute('app_store_new_partner');
            }
    }
 

    #[Route('/new/store', name: 'app_store_new_partner', methods: ['GET', 'POST'])]
    public function newStore(Request $request,Security $security): Response
    {
        $user = $this->getUser();
        // Get the image associated with the user
        $image = $user->getImage();

        $store = new Store();
        $user = $security->getUser();
        $form = $this->createForm(StoreType::class, $store);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $store->setUser($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($store);
            $entityManager->flush();
            $this->addFlash('success', 'Store created successfully!');
            return $this->redirectToRoute('app_store_show_partner', ['id' => $store->getId()]);
            

        }

        return $this->render('partner/store/new.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'image' => $image,
        ]);
    }
 
    #[Route('/show/{id?}', name: 'app_store_show_partner')]
    public function showStore(?int $id, StoreRepository $storeRepository, RatingRepository $ratingRepository): Response
    {//TODO: add the rating methode and test if it is first time create a new insert else do an edit 
        $user = $this->getUser();
        $image = $user->getImage();  
        if ($id === null) {
            return $this->redirectToRoute('app_store_new_partner');
        } else {
            $store = $storeRepository->find($id);
            $rating = $ratingRepository->getAverageStoreRating($id);
            // addflash

            // $this->addFlash('success', 'Store updated successfully!');
            return $this->render('partner/store/show.html.twig', [
            'store' => $store,
            'rating' => $rating,
            'user' => $user,
            'image' => $image
            ]);
        }
    }

    #[Route('/edit/{id}', name: 'app_store_edit_partner', methods: ['GET', 'POST'])]
    public function editStore(Request $request, Store $store, StoreRepository $storeRepository): Response
    {
        $user = $this->getUser();
        // Get the image associated with the user
        $image = $user->getImage();

        $form = $this->createForm(StoreType::class, $store);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $storeRepository->save($store, true);
            $this->addFlash('success', 'Store updated successfully!');

            return $this->redirectToRoute('app_partner', [], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('partner/store/edit.html.twig', [
            'store' => $store,
            'form' => $form ,
            'user' => $user,  
            'image' => $image,    
        ]);
    }
    
// .........................................Gestion Product Store..........................................................

    
    #[Route('/new/produit/{id}', name: 'app_product_new')]
    public function newProductinStore(Request $request, PersistenceManagerRegistry $doctrine, Security $security,$id): Response
    {
        $entityManager = $doctrine->getManager();
        $store = $entityManager->getRepository(Store::class)->find($id);

        if (!$store) {
            throw $this->createNotFoundException('Store with ID '.$id.' not found');
        }

        $product = new Produit();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $store->addProduit($product);
            $entityManager->persist($product);
            $entityManager->flush();

            // $accountSid = 'ACbc46db0068633b4bdf2fd949f0c11672';
            // $authToken = '01578a75bd79f53a0d95d62e82fb90cf';
            // $fromNumber = '+16812025444';
        
            // // Instantiate the Twilio client
            // $twilio = new Client($accountSid, $authToken);
        
            // // Instantiate the SendSmsService and set the required parameters
            // $sms = new SendSmsService();
            // $sms->setAccountSid($accountSid);
            // $sms->setAuthToken($authToken);
            // $sms->setFromNumber($fromNumber);
            // $sms->setClient($twilio);

            // $admin = new User();
            // $admin->setPhone('+21621184125');
        
            // // Send an SMS to the user
            // $sms->send($admin->getPhone(), 'You have a new product pending approval.');


            $this->addFlash('success', 'Product created successfully!');
            return $this->redirectToRoute('app_products_store_liste');
        }

       

        $user = $this->getUser();
        $image = $user->getImage(); 

        return $this->render('partner/produit/addProduit.html.twig', [
        'form' => $form->createView(),
        'image' => $image ,
        'user' => $user
        ]);
    }


    #[Route('/products_store/liste', name: 'app_products_store_liste')]
    public function productsinStore(Request $request, PersistenceManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $image = $user->getImage();

        $store = $doctrine->getManager()->getRepository(Store::class)->findOneBy(['user' => $user]);

        if (!$store) {
            throw $this->createNotFoundException('Store not found for user '.$user->getId());
        }

        $produits = $store->getProduit();

        return $this->render('partner/ListProductInStore.html.twig', [
            'produits' => $produits,
            'image' => $image,
            'store' => $store,
            'user' =>$user
        ]);
    }


    #[Route('/updateProduct/{id}', name: 'app_updateProduct')]
    public function updateProduct($id, Request $request, ProduitRepository $rep, ManagerRegistry $doctrine): Response
    {
        // Get the current user
        $user = $this->getUser();
        // Get the image associated with the user
        $image = $user->getImage();
        // récupérer la classe à modifier
        $produits = $rep->find($id);
        // créer un formulaire
        $form = $this->createForm(ProductType::class, $produits);
        // récupérer les données saisies
        $form->handleRequest($request);
        // vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // récupérer les données saisies
            $produits = $form->getData();
            // persister les données
            $rep = $doctrine->getManager();
            $rep->persist($produits);
            $rep->flush();
            //flash message
            $this->addFlash('success', 'Product updated successfully!');
            return $this->redirectToRoute('app_products_store_liste');
        }
        return $this->render('partner/produit/editProduct.html.twig', [
            'form' => $form->createView(),
            'image' => $image,
            'user' => $user
        ]);
    }


    #[Route('/deleteProduct/{id}', name: 'app_deleteProduct')]
    public function deleteProduct($id, ProduitRepository $rep, ManagerRegistry $doctrine ): Response
    {
        //recuperer la classe a supprimer
        $produits = $rep->find($id);
        $rep=$doctrine->getManager();
        //supprimer la classe        
        $rep->remove($produits);
        $rep->flush();
        //flash message
        $this->addFlash('success', 'Product deleted successfully!');
        return $this->redirectToRoute('app_products_store_liste'); 
        
    }


// .........................................Gestion Commande..........................................................


    #[Route('/commands', name: 'app_par_commands')]
    public function partnercommande(Request $request, ManagerRegistry $doctrine): Response
    {
        $user=$this->getUser();
        $store = $doctrine->getRepository(Store::class)->findBy(['user'=>$user->getId()]);
        // Get the query parameters from the URL
        $etat = $request->query->get('etat');
        $order = $request->query->get('prixOrder');
        $etatswitch = $request->query->get('etatswitch');
        $commandedetail = $request->query->get('commandedetail');
        $displaydetail = null;
        // neeeds to completed the prix filter  gonna update and try few things and comeback to this wael3
        // Get the commandes and details from the database

        //  $commande = $doctrine->getRepository(Commande::class)->findByStore($client,$etat,$min,$max,$order);
        if ($commandedetail !== null && ($etatswitch === "Completed" || $etatswitch === "Pending"  || $etatswitch === "Progress")) {
            $detail = $doctrine->getRepository(DetailCommande::class)->find($commandedetail);

            $detail->setEtat($etatswitch);
            $doctrine->getManager()->persist($detail);
            $doctrine->getManager()->flush();
            $entityManager = $doctrine->getManager();

            // Get the original commande
            $originalCommande = $detail->getCommande();

            // Get all details associated with the original commande
            $details = $doctrine->getRepository(DetailCommande::class)->findBy(['commande' => $originalCommande]);

            // Determine the minimum etat among all details
            $countPending = 0;
            $countProgress = 0;
            $countCompleted = 0;
            foreach ($details as $d) {
                if ($d->getEtat() === "Pending") {
                    $countPending++;
                }
                if ($d->getEtat() === "Progress") {
                    $countProgress++;
                }
                if ($d->getEtat() === "Completed" || $d->getEtat() === "Canceled" ) {
                    $countCompleted++;;
                }
            }

            // Update the etat of the original commande if the minimum etat is less than the current etat
            $currentEtat = $originalCommande->getEtat();
            
                if ($countProgress === 0 && $countPending === 0) {
                    
                        $originalCommande->setEtat("Completed");
                        $entityManager->persist($originalCommande);
                        $entityManager->flush();
                    
                } else {
                    $originalCommande->setEtat("Progress");
                    $entityManager->persist($originalCommande);
                    $entityManager->flush();
                }
            
        }

        $displaydetail = $doctrine->getRepository(DetailCommande::class)->findByStore($store, $etat, $order);


        return $this->render('partner/commands.html.twig', [
            //     'historiquecommande' => $commande,
            'selecteddetails' => $displaydetail,
            'testinput' => $commandedetail,
            'user' => $user,
        ]);
    }
























}
