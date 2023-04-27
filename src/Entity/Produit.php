<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[Vich\Uploadable]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[Vich\UploadableField(mapping: 'product_image', fileNameProperty: 'photo')]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255)]
    private ?string $photo = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column(options: ['default' => 0])]
    private ?int $etat = 0;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    private ?Categorie $categorie = null;
    
    #[ORM\ManyToMany(targetEntity: Store::class, mappedBy: 'produit')]
    private Collection $stores;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: DetailCommande::class)]
    private Collection $detailCommandes;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: Reclamation::class)]
    private Collection $reclamations;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: RatingProduit::class)]
    private Collection $ratingProduits;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: Likedislike::class)]
    private Collection $likedislikes;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: Commentaire::class)]
    private Collection $commentaires;

    public function __construct()
    {
        $this->stores = new ArrayCollection();
        $this->detailCommandes = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
        $this->ratingProduits = new ArrayCollection();
        $this->likedislikes = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, Store>
     */
    public function getStores(): Collection
    {
        return $this->stores;
    }

    public function addStore(Store $store): self
    {
        if (!$this->stores->contains($store)) {
            $this->stores->add($store);
            $store->addProduit($this);
        }

        return $this;
    }

    public function removeStore(Store $store): self
    {
        if ($this->stores->removeElement($store)) {
            $store->removeProduit($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, DetailCommande>
     */
    public function getDetailCommandes(): Collection
    {
        return $this->detailCommandes;
    }

    public function addDetailCommande(DetailCommande $detailCommande): self
    {
        if (!$this->detailCommandes->contains($detailCommande)) {
            $this->detailCommandes->add($detailCommande);
            $detailCommande->setProduit($this);
        }

        return $this;
    }

    public function removeDetailCommande(DetailCommande $detailCommande): self
    {
        if ($this->detailCommandes->removeElement($detailCommande)) {
            // set the owning side to null (unless already changed)
            if ($detailCommande->getProduit() === $this) {
                $detailCommande->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): self
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations->add($reclamation);
            $reclamation->setProduit($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): self
    {
        if ($this->reclamations->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getProduit() === $this) {
                $reclamation->setProduit(null);
            }
        }

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
            $ratingProduit->setProduit($this);
        }

        return $this;
    }

    public function removeRatingProduit(RatingProduit $ratingProduit): self
    {
        if ($this->ratingProduits->removeElement($ratingProduit)) {
            // set the owning side to null (unless already changed)
            if ($ratingProduit->getProduit() === $this) {
                $ratingProduit->setProduit(null);
            }
        }

        return $this;
    }

    public function setImageFile(File $photo = null)
    {
        $this->imageFile = $photo;

        if ($photo) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
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
            $likedislike->setProduit($this);
        }

        return $this;
    }

    public function removeLikedislike(Likedislike $likedislike): self
    {
        if ($this->likedislikes->removeElement($likedislike)) {
            // set the owning side to null (unless already changed)
            if ($likedislike->getProduit() === $this) {
                $likedislike->setProduit(null);
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
            $commentaire->setProduit($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getProduit() === $this) {
                $commentaire->setProduit(null);
            }
        }

        return $this;
    }
}
