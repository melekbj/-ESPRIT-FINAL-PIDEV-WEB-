<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
// use Symfony\Component\Serializer\Annotation\Ignore;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Serializer\Annotation\Ignore ;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;



#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['phone'], message: 'There is already an account with this phone number')]
#[Vich\Uploadable]


class User implements UserInterface, PasswordAuthenticatedUserInterface, \Serializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Email should not be blank')]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $roles = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: 'Passowrd should not be blank')]
    #[Assert\Length(
        min: 8,
        minMessage: 'The password must be at least {{ limit }} characters long.'
    )]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Name should not be blank')]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Family name should not be blank')]
    private ?string $prenom = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Age should not be blank')]
    private ?int $age = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Adresse should not be blank')]
    private ?string $adresse = null;
    
    // /**
    //  * @ORM\Column(type="datetime", nullable=true)
    //  * @var \DateTimeInterface|null
    //  */
    // private $imageUpdatedAt;

    #[Vich\UploadableField(mapping: 'user_image', fileNameProperty: 'image')]
    public ?File $imageFile = null;

    #[ORM\Column(length: 255, nullable:true)]
    private ?string $image = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(message: 'Family name should not be blank')]
    private ?string $genre = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank(message: 'Phone number should not be blank')]
    #[Assert\Regex(
        pattern: '/^\+?\d*$/',
        message: 'Phone number should be in the format: +XXXXXXXXXXXX'
    )]
    private ?string $phone = null;
    

    #[ORM\Column(options: ['default' => 0])]
    private ?int $etat = 0;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Produit::class)]
    private Collection $produit;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Commande::class)]
    private Collection $commandes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reclamation::class)]
    private Collection $reclamations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reservation::class)]
    private Collection $reservations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Rating::class)]
    private Collection $ratings;

    public function __construct()
    {
        $this->produit = new ArrayCollection();
        $this->commandes = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        // set default value for etat field
        // $this->etat = 0;
        $this->ratingProduits = new ArrayCollection();
        $this->likedislikes = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        // $this->reclam = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

      /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): string
    {
        return $this->roles;
        // guarantee every user at least has ROLE_USER
        // $roles[] = 'ROLE_USER';

        // return array_unique($roles);
    }

    public function setRoles(string $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

     /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getImage(): ?string
    {
        if ($this->image === null) {
            return null;
        }
        return $this->image;
    }


    public function setImage(?string $image): void
    {
        $this->image = $image;
    }


    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduit(): Collection
    {
        return $this->produit;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produit->contains($produit)) {
            $this->produit->add($produit);
            $produit->setUser($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        if ($this->produit->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getUser() === $this) {
                $produit->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setUser($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getUser() === $this) {
                $commande->setUser(null);
            }
        }

        return $this;
    }

    // /**
    //  * @return Collection<int, Reclamation>
    //  */
    // public function getReclamations(): Collection
    // {
    //     return $this->reclamations;
    // }

    // public function addReclamation(Reclamation $reclamation): self
    // {
    //     if (!$this->reclamations->contains($reclamation)) {
    //         $this->reclamations->add($reclamation);
    //         $reclamation->setClient($this);
    //     }

    //     return $this;
    // }

    // public function removeReclamation(Reclamation $reclamation): self
    // {
    //     if ($this->reclamations->removeElement($reclamation)) {
    //         // set the owning side to null (unless already changed)
    //         if ($reclamation->getClient() === $this) {
    //             $reclamation->setClient(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setUser($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getUser() === $this) {
                $reservation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setUser($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): self
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getUser() === $this) {
                $rating->setUser(null);
            }
        }

        return $this;
    }

   
    #[ORM\Column(length: 255,nullable:true)]
    private ?string $resetToken;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: RatingProduit::class)]
    private Collection $ratingProduits;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Likedislike::class)]
    private Collection $likedislikes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Commentaire::class)]
    private Collection $commentaires;

    // #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reclamation::class)]
    // private Collection $reclam;

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    // second

    public function setForgotPasswordToken(string $token): self
    {
        $this->forgotPasswordToken = $token;

        return $this;
    }
    public function setForgotPasswordTokenCreatedAt(\DateTimeImmutable $dateTime): self
    {
        $this->forgotPasswordTokenCreatedAt = $dateTime;
    
        return $this;
    }

    public function setForgotPasswordTokenMustBeVerifiedBefore(\DateTimeImmutable $dateTime): self
    {
        $this->forgotPasswordTokenMustBeVerifiedBefore = $dateTime;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * @return Collection<int, RatingProduit>
     */
    public function getRatingProduits(): Collection
    {
        return $this->ratingProduits;
    }

    public function addRatingProduit(RatingProduit $ratingProduit): self
    {
        if (!$this->ratingProduits->contains($ratingProduit)) {
            $this->ratingProduits->add($ratingProduit);
            $ratingProduit->setUser($this);
        }

        return $this;
    }

    public function removeRatingProduit(RatingProduit $ratingProduit): self
    {
        if ($this->ratingProduits->removeElement($ratingProduit)) {
            // set the owning side to null (unless already changed)
            if ($ratingProduit->getUser() === $this) {
                $ratingProduit->setUser(null);
            }
        }

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): void
    {
        $this->imageFile = $imageFile;

        // This is important to trigger the file upload
        if ($imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
            
            
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password,
        ) = unserialize($serialized);
    }

    /**
     * @return Collection<int, Likedislike>
     */
    public function getLikedislikes(): Collection
    {
        return $this->likedislikes;
    }

    public function addLikedislike(Likedislike $likedislike): self
    {
        if (!$this->likedislikes->contains($likedislike)) {
            $this->likedislikes->add($likedislike);
            $likedislike->setUser($this);
        }

        return $this;
    }

    public function removeLikedislike(Likedislike $likedislike): self
    {
        if ($this->likedislikes->removeElement($likedislike)) {
            // set the owning side to null (unless already changed)
            if ($likedislike->getUser() === $this) {
                $likedislike->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setUser($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getUser() === $this) {
                $commentaire->setUser(null);
            }
        }

        return $this;
    }

    

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamation(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamations): self
    {
        if (!$this->reclamations->contains($reclamations)) {
            $this->reclamations->add($reclamations);
            $reclamations->setUser($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamations): self
    {
        if ($this->reclamations->removeElement($reclamations)) {
            // set the owning side to null (unless already changed)
            if ($reclamations->getUser() === $this) {
                $reclamations->setUser(null);
            }
        }

        return $this;
    }

    
    
}
