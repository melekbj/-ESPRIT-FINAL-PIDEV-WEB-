<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserJsonController extends AbstractController
{
    #[Route('/user/json', name: 'app_user_json')]
    public function index(): Response
    {
        return $this->render('user_json/index.html.twig', [
            'controller_name' => 'UserJsonController',
        ]);
    }

    #[Route('/registerJson', name: 'app_registration_json')]
    public function registration(
    Request $request,
    PersistenceManagerRegistry $doctrine, 
    UserPasswordHasherInterface $passwordHashed,
    NormalizerInterface $normalizer
    ): Response
    {
        
        $em = $doctrine->getManager();
        $user = new User();
        $user
            ->setEmail('tasnime@gmail.com')
            ->setPassword($passwordHashed->hashPassword($user, '123456'))
            ->setRoles('ROLE_PARTNER')
            ->setNom('Melek')
            ->setPrenom('Bojboj')
            ->setAdresse('Tunis')
            ->setPhone('525252525')
            ->setGenre('homme')
            ->setVille('ahla')
            ->setAge(10);

        $em->persist($user);
        $em->flush();
        $jsonContent = $normalizer->normalize($user, 'json', ['groups' => 'addUser']);
        $retour = json_encode($jsonContent);
        return new Response($retour);

    }


    #[Route('/listeUsers', name: 'liste_users')]
    public function EventsTypes(
        Request $request,
        NormalizerInterface $normalizer, 
        PersistenceManagerRegistry $doctrine, 
        EntityManagerInterface $entityManager
    ): Response
    {
        $userRepo = $entityManager->getRepository(User::class);
        $users = $userRepo->findAll();
        $usersNormalises = $normalizer->normalize($users, 'json', ['groups'=>"addUser"]);
        $usersJson = json_encode($usersNormalises);
        $response = new Response($usersJson);

        return $response;
    }








}
