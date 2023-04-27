<?php

namespace App\Controller;

use App\Entity\Store;
use App\Form\UserType;
use App\Entity\Produit;
use App\Form\StoreType;
use App\Form\ProductType;
use App\Entity\CategorieStore;
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

    // #[Route('/categorie_store', name: 'app_categorie_store_index', methods: ['GET'])]
    // public function ListCategorie(CategorieStoreRepository $categorieStoreRepository): Response
    // {
    //     $cat = $entityManager->getRepository(CategorieStore::class);
    //     $categoriestores = $cat->findAll();

    //     return $this->render('admin/categorie_store/index.html.twig', [
    //         'categorie_store' => $categoriestores
    //     ]);
    // }

    #[Route('/categorie_store/new', name: 'app_categorie_store_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategorieStoreRepository $categorieStoreRepository): Response
    {
        $categorieStore = new CategorieStore();
        $form = $this->createForm(CategorieStoreType::class, $categorieStore);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $categorieStoreRepository->save($categorieStore, true);
    
                return $this->redirectToRoute('app_categorie_store_index', [], Response::HTTP_SEE_OTHER);
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'This category already exists.');
                // Render the form again with the error message
                return $this->renderForm('admin/categorie_store/new.html.twig', [
                    'categorie_store' => $categorieStore,
                    'form' =>  $form,
                ]);
            }
        }

        return $this->renderForm('admin/categorie_store/new.html.twig', [
            'categorie_store' => $categorieStore,
            'form' =>  $form,
        ]);
    }

    #[Route('/categorie_store/{id}', name: 'app_categorie_store_show', methods: ['GET'])]
    public function show(CategorieStore $categorieStore): Response
    {
        return $this->render('admin/categorie_store/show.html.twig', [
            'categorie_store' => $categorieStore,
        ]);
    }

    #[Route('/categorie_store/{id}/edit', name: 'app_categorie_store_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategorieStore $categorieStore, CategorieStoreRepository $categorieStoreRepository): Response
    {
        $form = $this->createForm(CategorieStoreType::class, $categorieStore);
        $form->handleRequest($request);

    
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $categorieStoreRepository->save($categorieStore, true);
    
                return $this->redirectToRoute('app_categorie_store_index', [], Response::HTTP_SEE_OTHER);
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'This category already exists.');
                // Render the form again with the error message
                return $this->renderForm('admin/categorie_store/edit.html.twig', [
                    'categorie_store' => $categorieStore,
                    'form' =>  $form,
                ]);
            }
        }

        return $this->renderForm('admin/categorie_store/edit.html.twig', [
            'categorie_store' => $categorieStore,
            'form' =>  $form,
        ]);
    }

    #[Route('/categorie_store/{id}', name: 'app_categorie_store_delete', methods: ['POST'])]
    public function delete(Request $request, $id, CategorieStore $categorieStore, StoreRepository $StoreRepository, CategorieStoreRepository $categorieStoreRepository): Response
    {
        $storedefault = $categorieStoreRepository->find('37');
        $stores = $StoreRepository->findBy(['categorie' => $id]);
        foreach ($stores as $store) {
            $store->setCategorie($storedefault);
            $StoreRepository->save($store, true);
        }
        if ($this->isCsrfTokenValid('delete' . $categorieStore->getId(), $request->request->get('_token'))) {
            $categorieStoreRepository->remove($categorieStore, true);
        }
        return $this->redirectToRoute('app_categorie_store_index', [], Response::HTTP_SEE_OTHER);
    }

   
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

            // return $this->redirectToRoute('app_store_show_partner', ['id' => $id]);
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


























}
