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

        $produitRepository = $entityManager->getRepository(Produit::class);
        $produits = $produitRepository->findBy(['etat' => [1]]);

        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'events' => $events,
            'stores' => $stores,
            'produits' => $produits,
        ]);
    }

    #[Route('/products', name: 'app_products')]
    public function produitIndex(EntityManagerInterface $entityManager): Response
    {
        $produitRepository = $entityManager->getRepository(Produit::class);
        $produits = $produitRepository->findBy(['etat' => [1]]);

        
        
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
        $evenement = $rep->find($id);
        $event = new Reservation();
        $event->setUser($user); // set the authenticated user in the $event object
        $event->setDate(new \DateTime()); // set the authenticated user in the $event object
        $event->setEvent($evenement);
        $form = $this->createForm(ReservationType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            
            $nbPlacesReserved = $event->getNbPlaces();
            $availablePlaces = $evenement->getNbMax();

            // Decrement the nbMax field by the number of places reserved
            $evenement->setNbMax($evenement->getNbMax() - $nbPlacesReserved);

            

            if ($nbPlacesReserved > 10) { // Check if the user tries to reserve more than 10 places
                $this->addFlash('error', 'Vous ne pouvez pas réserver plus de 10 places.');
            } else {
                // Check if there are enough places available
                if ($availablePlaces >= $nbPlacesReserved) {
                    // Decrement the nbPlaces field by the number of places reserved
                    $event->setNbPlaces($nbPlacesReserved);
                    $entityManager = $doctrine->getManager();
                    $entityManager->persist($event);
                    $entityManager->flush();
                    $evenement->setNbMax($availablePlaces - $nbPlacesReserved);
                    $entityManager->persist($evenement);
                    $entityManager->flush();
                    $this->addFlash('success', 'Reservation ajouté avec succès. Places restantes: '. 10 - $event->getNbPlaces());
                } else {
                    $this->addFlash('error', 'Il ne reste que '.$availablePlaces.' places disponibles pour cet événement.');
                }
            }
        }

        return $this->render('home/detailEvent.html.twig', [
            'evenement' => $evenement,
            // 'image' => $image,
            'form' =>$form->createView(),
        ]);
    }

    #[Route('/detailStore/{id}', name: 'app_stores_detail', methods: ["GET", "POST"] )]
    public function showStore($id, EntityManagerInterface $entityManager, StoreRepository $rep, Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $store = $rep->find($id);
        
        $produitRepository = $entityManager->getRepository(Produit::class);
        $produits = $produitRepository->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('p.stores', 's')
            ->where('p.etat = :etat')
            ->andWhere('s.id = :storeId')
            ->setParameter('etat', 1)
            ->setParameter('storeId', $store->getId())
            ->getQuery()
            ->getResult();

        return $this->render('home/detailStore.html.twig', [
            'stores' => $store,
            'produits' => $produits,
        ]);
    }








}
