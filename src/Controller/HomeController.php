<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Store;
use App\Entity\Produit;
use App\Entity\Evenement;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Service\QrcodeService;
use App\Repository\StoreRepository;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $eventrepo = $entityManager->getRepository(Evenement::class);
        $events = $eventrepo->findAll();

        $storeRepo = $entityManager->getRepository(Store::class);
        $stores = $storeRepo->findAll();

        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'events' => $events,
            'stores' => $stores,
        ]);
    }

    #[Route('/products', name: 'app_products')]
    public function produitIndex(EntityManagerInterface $entityManager): Response
    {
        $produitRepository = $entityManager->getRepository(Produit::class);
        $produits = $produitRepository->findAll();

        
        
        return $this->render('home/products.html.twig', [
            'controller_name' => 'HomeController',
            'produits' => $produits,
            
        ]);
    }

    // #[Route('/events', name: 'app_products')]
    // public function produitIndex(EntityManagerInterface $entityManager): Response
    // {
    //     $produitRepository = $entityManager->getRepository(Produit::class);
    //     $produits = $produitRepository->findAll();
        
    //     return $this->render('home/products.html.twig', [
    //         'controller_name' => 'HomeController',
    //         'produits' => $produits,
    //     ]);
    // }

    #[Route('products/details', name: 'app_detail')]
    public function storeIndex(): Response
    {
        return $this->render('home/detail.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function aboutIndex(QrcodeService $qrcodeService): Response
    {
        $qrcodeDataUri = $qrcodeService->qrcode();
        return $this->render('home/about.html.twig', [
            'controller_name' => 'HomeController',
            'qrcode_data_uri' => $qrcodeDataUri,
            
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contactIndex(): Response
    {
        return $this->render('home/contact.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


    #[Route('/detailEvent/{id}', name: 'app_events_detail', methods: ["GET", "POST"] )]
    public function showev($id, EvenementRepository $rep, Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        // Get the image associated with the user
        // $image = $user->getImage();
        //Utiliser find by id
        $evenement = $rep->find($id);
        // dd($evenement);

        $event = new Reservation();
        $event->setUser($user); // set the authenticated user in the $event object
        $event->setEvent($evenement); // set the event based on the $id parameter
        $event->setDate(new \DateTime()); // set the authenticated user in the $event object
        $form = $this->createForm(ReservationType::class, $event);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $doctrine->getManager();
            $entityManager->persist($event);
            $entityManager->flush();
            $this->addFlash('success', 'Reservation ajouté avec succès');
            if ($this->getUser()->getRoles() == 'ROLE_PARTNER') {
                return $this->redirectToRoute('app_partner');
            } else {
                return $this->redirectToRoute('app_client_index');
            }
        }

        return $this->render('home/detailEvent.html.twig', [
            'evenement' => $evenement,
            // 'image' => $image,
            'form' =>$form->createView(),
        ]);
    }

    #[Route('/detailStore/{id}', name: 'app_stores_detail', methods: ["GET", "POST"] )]
    public function showStore($id, StoreRepository $rep, Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        // Get the image associated with the user
        // $image = $user->getImage();
        //Utiliser find by id
        $stores = $rep->find($id);
        // dd($evenement);

        // $event = new Reservation();
        // $event->setUser($user); // set the authenticated user in the $event object
        // $event->setEvent($evenement); // set the event based on the $id parameter
        // $form = $this->createForm(ReservationType::class, $event);
        // $form->handleRequest($request);

        // if($form->isSubmitted() && $form->isValid()){
        //     $entityManager = $doctrine->getManager();
        //     $entityManager->persist($event);
        //     $entityManager->flush();
        //     $this->addFlash('success', 'Reservation ajouté avec succès');
        //     if ($this->getUser()->getRoles() == 'ROLE_PARTNER') {
        //         return $this->redirectToRoute('app_partner');
        //     } else {
        //         return $this->redirectToRoute('app_client_index');
        //     }
        // }

        return $this->render('home/detailStore.html.twig', [
            'stores' => $stores,
            // 'image' => $image,
            // 'form' =>$form->createView(),
        ]);
    }






}
