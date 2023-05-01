<?php

namespace App\Controller;

use App\Entity\Likedislike;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProduitRepository;
use App\Repository\LikedislikeRepository;


class LikedislikeController extends AbstractController
{
    #[Route('/likedislike', name: 'app_likedislike')]
    public function index(): Response
    {
        return $this->render('likedislike/index.html.twig', [
            'controller_name' => 'LikedislikeController',
        ]);
    }
    #[Route('/new/{idproduit}/{value}', name: 'app_like_dislike_add', methods: ['GET', 'POST'])]
    public function new(LikedislikeRepository $likedislikeRepository ,ProduitRepository $produitRepository ,$idproduit,$value): Response
    { 
        if($this->getUser()){
            $produit=$produitRepository->find($idproduit);
            $isAlreadyClicked=$likedislikeRepository->findOneBy(["user"=>$this->getUser(),"produit"=>$produit]);
            if($isAlreadyClicked){
                if($isAlreadyClicked->getValue()==$value)
{                $likedislikeRepository->remove($isAlreadyClicked,true);
}                else
                {$isAlreadyClicked->setValue($value);
                $likedislikeRepository->save($isAlreadyClicked,true);}


            }else{
                $like = new Likedislike();
            $like->setValue($value);
            $like->setProduit($produit);
            $like->setUser($this->getUser());
            $likedislikeRepository->save($like,true);
            }
            return $this->redirectToRoute('app_products', [], Response::HTTP_SEE_OTHER);

        }else{
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
 
        }

      
       

   }
    
}
