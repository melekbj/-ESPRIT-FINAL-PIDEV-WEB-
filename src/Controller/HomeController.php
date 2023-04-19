<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Produit;
use App\Entity\Evenement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $eventrepo = $entityManager->getRepository(Evenement::class);
        $events = $eventrepo->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'events' => $events,
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
    public function aboutIndex(): Response
    {
        return $this->render('home/about.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contactIndex(): Response
    {
        return $this->render('home/contact.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }







}
