<?php

namespace App\Controller;

use App\Entity\Commande;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ClientController extends AbstractController
{
    #[Route('/client', name: 'app_client')]
    public function index(): Response
    {
        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }

    #[Route('/listcommande', name: 'app_commande')]
    public function readC(): Response
    {
        //recuperer le repository
        $repository = $this->getDoctrine()->getRepository(Commande::class);
        //utiliser findAll() pour recuperer toutes les classes
        $commande = $repository->findAll();
        return $this->render('client/listcommande.html.twig', [
            'c' => $commande,
        ]);
    }




}
