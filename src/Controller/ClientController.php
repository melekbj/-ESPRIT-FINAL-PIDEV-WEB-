<?php

namespace App\Controller;

use Stripe\Card;
use Stripe\Token;
use Stripe\Charge;
use Stripe\Refund;
use Stripe\Stripe;
use Stripe\Customer;
use App\Form\UserType;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Form\PaymentType;
use App\Entity\Reclamation;
use App\Entity\Reservation;
use App\Form\ReclamationType;
use App\Form\ReservationType;
use App\Entity\DetailCommande;
use App\Entity\TypeReclamation;
use App\Repository\CommandeRepository;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
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
    

    #[Route('/', name: 'app_client_index')]
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


// .........................................Gestion Reclamation..........................................................

    #[Route('/addReclamationProduit/{id}', name: 'app_reclamation')]
    public function addAction($id,Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, int $userId = null): Response
    {
        $user = $this->getUser();
        $image = $user->getImage();

        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamation->setUser($user);
            $reclamation->setDate(new \DateTime());
            $reclamation->setEtat("pending");
            $reclamation->setProduit($entityManager->getRepository(DetailCommande::class)->find($id)->getProduit());
            $reclamation->setCommande($entityManager->getRepository(DetailCommande::class)->find($id)->getCommande());
            $entityManager->persist($reclamation);
            $entityManager->flush();
            $this->addFlash('success', 'Reclamation added successfully!');
            return $this->redirectToRoute('app_reclamation_list');
        }


        return $this->render('client/reclamation/add.html.twig', [
            'form' => $form->createView(),
            'image' => $image,
        ]);
    }



    #[Route('/liste_reclamation', name: 'app_reclamation_list')]
    public function ListReclamations(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $userId = $user->getId();
        // Get the image associated with the user
        $image = $user->getImage();

        $reclamationRepository = $entityManager->getRepository(Reclamation::class);
        $reclamations = $reclamationRepository->findBy(['user' => $userId]);

        return $this->render('client/historiqueReclamation.html.twig', [
            'reclamations' => $reclamations,
            'image' => $image,
        ]);
    }

    #[Route('/liste_reservation', name: 'app_reservation_list')]
    public function ListReservations(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $userId = $user->getId();
        // Get the image associated with the user
        $image = $user->getImage();

        $reserRepo = $entityManager->getRepository(Reservation::class);
        $reservations = $reserRepo->findBy(['user' => $userId]);

        return $this->render('client/historiqueReservation.html.twig', [
            'reservations' => $reservations,
            'image' => $image,
        ]);
    }


// .........................................Gestion Commande..........................................................

    #[Route('/historique', name: 'app_historique')]
    public function historique(Request $request, ManagerRegistry $doctrine, CommandeRepository $cc): Response
    {
        $user = $this->getUser();
        $image = $user->getImage();
        $client = $user->getId();
        // $client = '1';
        // Get the query parameters from the URL
        $etat = $request->query->get('etat');
        $order = $request->query->get('prixOrder');
        $min = $request->query->get('min');
        $max = $request->query->get('max');
        $commandedetail = $request->query->get('commandedetail');
        $displaydetail = null;
        // neeeds to completed the prix filter  gonna update and try few things and comeback to this wael3
        // Get the commandes and details from the database

        // $commande = $doctrine->getRepository(Commande::class)->findByClientByEtatByPrice2($client, $etat, $min, $max, $order);
        $commande = $cc->findByClientByEtatByPrice2($client, $etat, $min, $max, $order);

        $displaydetail = $doctrine->getRepository(DetailCommande::class)->findByCommande($commandedetail);

        return $this->render('client/historiqueCommande.html.twig', [
            'historiquecommande' => $commande,
            'selecteddetails' => $displaydetail,
            'testinput' => $commandedetail,
            'image' => $image,
        ]);
    }
    // loads a list of product for safa ( just need the button )
    #[Route('/products', name: 'app_viewproduit')]
    public function listview(ManagerRegistry $doctrine): Response
    {

        $produits = $doctrine->getRepository(Produit::class)->findAll();


        return $this->render('home/products.html.twig', [
            'produits' => $produits,
        ]);
    }


    #[Route('/cart/add/{id}', name: 'app_cart_addscript')]
    public function listviewscript($id, SessionInterface $session, ManagerRegistry $doctrine, Request $request): Response
    {
        $cart = $session->get('cart', []);
        if (!empty($cart[$id])) {
            // makes sure to not execced the limit of availeble quantity
            $produit = $doctrine->getRepository(Produit::class)->find($id);
            if ($produit->getQuantite() > $cart[$id]) {

                $cart[$id]++;
            } else {
                $session->addFlash('carterror', 'you have exceeded the availeble quantity');
                // $this->addFlash('error', 'New password and confirm password do not match.');
            }
        } else {
            $cart[$id] = 1;
        }
        $session->set('cart', $cart);
        $referer = $request->headers->get('referer');

        return $this->redirect($referer);



        //------test purpose to clear all stored items inside the session--------
        //   $session->clear();
        //---------test session stored item cart----------
        //  dd($session->get('cart'));


    }
    #[Route('/cart/remove/{id}', name: 'app_cart_removescript')]
    public function listviewscript2($id, SessionInterface $session, ManagerRegistry $doctrine, Request $request): Response
    {
        $cart = $session->get('cart', []);
        if ($cart[$id] > 1) {
            $cart[$id]--;
        } else {
            unset($cart[$id]);
        }
        $session->set('cart', $cart);
        $referer = $request->headers->get('referer');

        return $this->redirect($referer);



        //------test purpose to clear all stored items inside the session--------
        //   $session->clear();
        //---------test session stored item cart----------
        //  dd($session->get('cart'));


    }
   

    #[Route('/cartview', name: 'app_cartview')]
    public function cartview(Request $request, ManagerRegistry $doctrine, SessionInterface $session): Response
    {

        // Get the session storage data sent in the request
        $cart = $session->get('cart', []);
        $error = $session->get('carterror', '');
        $session->remove('carterror');
        // Get the cart data from the session object

        // Retrieve the products from the database based on the cart data
        $produits = [];
        foreach ($cart as $itemId => $quantity) {
            $produit = $doctrine->getRepository(Produit::class)->find($itemId);
            if ($produit) {
                $produits[] = [
                    'produit' => $produit,
                    'quantity' => $quantity,
                ];
            }
        }

        // Render the cart view template with the product data
        return $this->render('client/commande/cartview.html.twig', [
            'produits' => $produits,
            'error' => $error,
        ]);
    }
    // button ajouter commadne
    #[Route('/cart/confirm', name: 'app_cart_confirm')]
    public function confirmercart(EntityManagerInterface $em, SessionInterface $session, Request $request)
    {
        // $client = '1';
        $user = $this->getUser();
        //  $client=$user->getId();
        //   $user = $em->getRepository(User::class)->find($client);

        $cart = $session->get('cart', []);

        if (empty($cart)) {
            $session->set('carterror', 'cart is empty.');
            return $this->redirectToRoute('app_cartview');
        }

        $totalPrice = 0;
        $command = new Commande();
        $command->setUser($user);
        $command->setDestination($request->request->get('destination'));
        if (strlen($request->request->get('destination')) <= 10) {
            $session->set('carterror', 'Destination not set or too short.');
            return $this->redirectToRoute('app_cartview');
        }
        $command->setDate(new \DateTime());
        $command->setEtat('Pending');
        foreach ($cart as $productId => $quantity) {
            $product = $em->getRepository(Produit::class)->find($productId);
            if ($product->getQuantite() < $quantity) {
                $session->set('carterror', 'some products may not have the correct amount of availeble quantitys');
                return $this->redirectToRoute('app_cartview');
                break;
            }
            $price = $product->getPrix() * $quantity;
            $totalPrice += $price;
            $detailCommande = new DetailCommande();
            $detailCommande->setCommande($command);
            $detailCommande->setProduit($product);
            $detailCommande->setStore($product->getStores()->first());
            $detailCommande->setQuantite($quantity);
            $detailCommande->setPrixTotal($price);
            $detailCommande->setEtat('Pending');
        }

        $command->setPrix($totalPrice);


        $session->set('commande', $command);

        if (!empty($cart) && strlen($request->request->get('destination')) > 10) {
            return $this->redirectToRoute('app_cart_payement');
        }


        return $this->redirectToRoute('app_historiquesecond');
    }

 

    #[Route('/cart/payment', name: 'app_cart_payement')]
    public function Payementprocess(EntityManagerInterface $em, SessionInterface $session, Request $request, ManagerRegistry $doctrine)
    {

        //  $client = '1';
        $user = $this->getUser();
        //   $client=$user->getId();
        //     $user = $em->getRepository(User::class)->find($client);

        $cart = $session->get('cart', []);

        foreach ($cart as $itemid => $quantity) {
            $produit = $doctrine->getRepository(Produit::class)->find($itemid);
            $produits[] = [
                'produit' => $produit,
                'quantity' => $quantity,
            ];
        }
        $command = $session->get('commande');
        $paymentForm = $this->createForm(PaymentType::class);
        $paymentForm->handleRequest($request);
        if ($paymentForm->isSubmitted() && $paymentForm->isValid()) {
            $paymentData = $paymentForm->getData();

            // Set your Stripe API secret key
            Stripe::setApiKey('sk_test_51Mf0S6FwJ7wXIwXewSc2z6FyXoFWAJZFy0Iuk4OZxzTVzLENEvBnnqug21baEIiV0MEDXTYl0y4Ajnp2LDWRZtC300mrwZe2j2');
            // create a card object with the informations
            $card = new Card();
            $card->number = $paymentData['cardNumber']; // 16 numere
             $card->exp_month = $paymentData['expirationMonth']; // 01-12
            $card->exp_year = $paymentData['expirationYear']; // > current year
            $card->cvc = $paymentData['cvc']; // secret code
            $card->address_zip = '12345';
            // Create a new Stripe customer
            // Create a new Stripe token from the card details
            $token = Token::create([
                'card' => [
                    'number' => $card->number,
                    'exp_month' => $card->exp_month,
                    'exp_year' => $card->exp_year,
                    'cvc' => $card->cvc,
                    'address_zip' => $card->address_zip,
                ],
            ]);
            // Create a new Stripe customer
            $customer = Customer::create([
                'email' => $user->getEmail(),
                'source' => $token->id, // pass the token ID as the value of 'source'
            ]);

            // Create a new Stripe charge
            try {
                $charge = Charge::create([
                    'amount' => $command->getPrix() * 100, // amount in cents
                    'currency' => 'usd', // or 'eur', 'gbp', etc.
                    'description' => 'My Awesome Ecommerce Payment',
                    'customer' => $customer->id, // add the customer to the charge
                ]);

                // on sucess 
                $totalPrice = 0;
                $command = $session->get('commande');
                $em->persist($command);
                $command->setPayment($charge->id);
                foreach ($cart as $productId => $quantity) {
                    $product = $em->getRepository(Produit::class)->find($productId);
                    $price = $product->getPrix() * $quantity;
                    $product->setQuantite($product->getQuantite() - $quantity);
                    $em->persist($product);
                    $detailCommande = new DetailCommande();
                    $detailCommande->setCommande($command);
                    $detailCommande->setProduit($product);
                    $detailCommande->setStore($product->getStores()->first());
                    $detailCommande->setQuantite($quantity);
                    $detailCommande->setPrixTotal($price);
                    $detailCommande->setEtat('Pending');
                    $em->persist($detailCommande);
                }

                $command->setUser($user);
                $em->flush();  // only after the charge is succesfull
                $session->remove('commande');
                $session->remove('cart');
            } catch (\Stripe\Exception\CardException $e) {
                // The card has been declined
                $session->set('carderror', $e->getMessage());
                return $this->redirectToRoute('app_cart_payement');
            }
            // if payment is sucess to redirect
            return $this->redirectToRoute('app_historique');
        }
        $error = $session->get('carderror' | '');
        return $this->render('client/commande/Payment.html.twig', [
            'produits' => $produits,
            'error' => $error,
            'paymentForm' => $paymentForm->createView(),
            'command' => $command,
        ]);
    }

    // not realy delete  and more like Cancel
    #[Route('/cancel/commande/{id}', name: 'app_commande_delete')]
    public function delete_commande(CommandeRepository $commandeRepository, $id, EntityManagerInterface $em, Request $request): Response
    {
        Stripe::setApiKey('sk_test_51Mf0S6FwJ7wXIwXewSc2z6FyXoFWAJZFy0Iuk4OZxzTVzLENEvBnnqug21baEIiV0MEDXTYl0y4Ajnp2LDWRZtC300mrwZe2j2');

        $commande = $commandeRepository->find($id);
        if ($commande->getEtat() === "Pending") {
            $chargeid = $commande->getPayment();
            $charge = Charge::retrieve($chargeid);
            $refund = Refund::create([
                'charge' => $charge->id,
                'amount' => $commande->getPrix() * 100,
            ]);

            if ($refund->status === 'succeeded') {
                // Refund was succefull
                $commande->setEtat("Canceled");
                $details = $commande->getDetailCommandes();
                foreach ($details as $detail) {
                    if ($detail->getEtat()!=="Completed")
                    {
                        $detail->setEtat("Canceled");
                        //adding the quantity back to the product
                        $detail->getProduit()->setQuantite($detail->getProduit()->getQuantite()+$detail->getQuantite());
                    }
                    # code...
                }
                $commande->setPrix('0');
                $em->persist($commande);
                $em->flush();
            }
        }



        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
    // not realy Delete and more like Cancel
    #[Route('/cancel/detailcommande/{id}', name: 'app_detailcommande_delete')]
    public function delete_detail_commande(DetailCommandeRepository $detailCommandeRepository, $id, EntityManagerInterface $em, Request $request): Response
    {
        // stripe Key usage :: this the third 3rd line in this page
        Stripe::setApiKey('sk_test_51Mf0S6FwJ7wXIwXewSc2z6FyXoFWAJZFy0Iuk4OZxzTVzLENEvBnnqug21baEIiV0MEDXTYl0y4Ajnp2LDWRZtC300mrwZe2j2');

        $detailcommande = $detailCommandeRepository->find($id);
        if ($detailcommande->getEtat() === "Pending") {
            $chargeid = $detailcommande->getCommande()->getPayment();
            // retrive the charge  from the ID
            $charge = Charge::retrieve($chargeid);
            // create the Refund from the Charge
            $refund = Refund::create([
                'charge' => $charge->id,
                'amount' => $detailcommande->getPrixTotal() * 100,
            ]);
            if ($refund->status === 'succeeded') {
                // Refund was succefull
                // Reduce the Command Total Prix
                $detailcommande->getCommande()->setPrix(
                    $detailcommande->getCommande()->getPrix() - $detailcommande->getPrixTotal()
                );
                $detailcommande->setEtat("Canceled");
                //adding the quantity back to the product
                $detailcommande->getProduit()->setQuantite($detailcommande->getProduit()->getQuantite()+$detailcommande->getQuantite());
            

                // Recalculate the ETAT of the entire Commande from a specific detailCommande
                $countPending = 0;
                $countProgress = 0;
                $countCompleted = 0;
                $countCanceled = 0;
                $totalcount = count($detailcommande->getCommande()->getDetailCommandes());
                foreach ($detailcommande->getCommande()->getDetailCommandes() as $d) {
                    if ($d->getEtat() === "Pending") {
                        $countPending++;
                    }
                    if ($d->getEtat() === "Progress") {
                        $countProgress++;
                    }
                    if ($d->getEtat() === "Completed" || $d->getEtat() === "Canceled") {
                        $countCompleted++;;
                    }
                    if ($d->getEtat() === "Canceled") {
                        $countCanceled++;;
                    }
                }
                    // Setting the Commande Etat when needed 
                if ($countProgress === 0 && $countPending === 0) {

                    if ($countCompleted !== 0 &&  $countCompleted === $totalcount && $countCanceled !== $totalcount) {

                        $detailcommande->getCommande()->setEtat("Completed");
                    } else {
                        $detailcommande->getCommande()->setEtat("Canceled");
                    }
                }
                // save the variable 
                // we use only 1 variable as it tracks everything within it
                $em->persist($detailcommande);
                $em->flush();
            }
        }

        // basicly it refresh the page with new informations
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
   

// .........................................gestion Reservation..........................................................

    #[Route('/deleteReservation/{id}', name: 'app_reservation_delete')]
    public function deleteReservation($id, ReservationRepository $reservationRepository, EvenementRepository $evenementRepository, PersistenceManagerRegistry $doctrine): Response
    {
        $reservation = $reservationRepository->find($id);
        $evenement = $evenementRepository->find($reservation->getEvent()->getId());
        
        if (!$reservation) {
            $this->addFlash('error', 'La réservation n\'a pas été trouvée.');
            return $this->redirectToRoute('app_reservation_list');
        }

        $reservationDate = $reservation->getDate();
        $currentDate = new \DateTime();

        $diff = $currentDate->diff($reservationDate);

        if ($diff->days > 0 || $diff->h > 24) {
            $this->addFlash('error', 'La réservation ne peut pas être supprimée car elle a plus de 24 heures.');
            return $this->redirectToRoute('app_reservation_list');
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($reservation);
        $evenement->setNbMax($evenement->getNbMax() + $reservation->getNbPlaces());
        $entityManager->persist($evenement);
        $entityManager->flush();

        $this->addFlash('success', 'La réservation a été supprimée avec succès.');

        return $this->redirectToRoute('app_reservation_list');
    } 


    



}