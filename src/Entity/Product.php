<?php

namespace App\Entity;


use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30, unique: true)]
    private ?string $ref = null;

    
    #[ORM\Column(length: 200)]
    #[Assert\NotBlank(message: 'Le nom du produit est obligatoire.')]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'Le nom du produit doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom du produit ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $name = null;

    #[ORM\Column(length: 220, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        max: 5000,
        maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Le prix HT est obligatoire.')]
    #[Assert\Positive(message: 'Le prix HT doit être supérieur à 0.')]
    private ?string $priceHt = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Le stock est obligatoire.')]
    #[Assert\PositiveOrZero(message: 'Le stock ne peut pas être négatif.')]
    private ?int $stock = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Le matériau ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $materiau = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Regex(
        pattern: '/^(M\d+(?:\.\d+)?|\d+(?:\.\d+)?mm)$/i',
        message: 'Le diamètre doit être au format M6 ou 4mm.'
    )]
    private ?string $diametreMm = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Positive(message: 'La longueur doit être supérieure à 0.')]
    private ?int $longueurMm = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Le type de tête ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $typeTete = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(
        max: 100,
        maxMessage: "Le type d'empreinte ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $typeEmpreinte = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\Choice(
        choices: ['piece', 'boite', 'sachet', 'kg'],
        message: 'L\'unité de vente doit être : pièce, boîte, sachet ou kilogramme.'
    )]
    private ?string $unite = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Positive(message: 'La quantité par conditionnement doit être supérieure à 0.')]
    private ?int $quantiteConditionnement = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'La catégorie est obligatoire.')]
    private ?Category $category = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'La TVA est obligatoire.')]
    private ?Tva $tva = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, ProductImage>
     */
    #[ORM\OneToMany(targetEntity: ProductImage::class, mappedBy: 'product', cascade: ['persist','remove'], orphanRemoval: true)]
    private Collection $productImages;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'product')]
    private Collection $orderItems;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Favorite::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $favorites;

    public function __construct()
    {
      $this->createdAt = new \DateTimeImmutable();
      $this->isActive = true;
      $this->productImages = new ArrayCollection();
      $this->orderItems = new ArrayCollection();
      $this->favorites = new ArrayCollection();
      $this->ref = 'REF-'.strtoupper(bin2hex(random_bytes(4)));
      
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPriceHt(): ?string
    {
        return $this->priceHt;
    }

    public function setPriceHt(?string $priceHt): static
    {
        $this->priceHt = $priceHt;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getTva(): ?Tva
    {
        return $this->tva;
    }

    public function setTva(?Tva $tva): static
    {
        $this->tva = $tva;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getPriceTtc(): float 
    {
      $rate = 0;
      if($this->tva)
        {
          $rate = $this->tva->getRate();
        }
      $priceTtc = $this->priceHt * (1 + ($rate / 100));
      return round($priceTtc, 2);
    }

    /**
     * @return Collection<int, ProductImage>
     */
    public function getProductImages(): Collection
    {
        return $this->productImages;
    }

    public function addProductImage(ProductImage $productImage): static
    {
        if (!$this->productImages->contains($productImage)) {
            $this->productImages->add($productImage);
            $productImage->setProduct($this);
        }

        return $this;
    }

    public function removeProductImage(ProductImage $productImage): static
    {
        if ($this->productImages->removeElement($productImage)) {
            // set the owning side to null (unless already changed)
            if ($productImage->getProduct() === $this) {
                $productImage->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setProduct($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getProduct() === $this) {
                $orderItem->setProduct(null);
            }
        }

        return $this;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(String $ref): static
    {
        $this->ref = $ref;

        return $this;
    }

    public function getMateriau(): ?string
    {
        return $this->materiau;
    }

    public function setMateriau(?string $materiau): static
    {
        $this->materiau = $materiau;

        return $this;
    }

    public function getDiametreMm(): ?string
    {
        return $this->diametreMm;
    }

    public function setDiametreMm(?string $diametreMm): static
    {
        $this->diametreMm = $diametreMm;

        return $this;
    }

    public function getLongueurMm(): ?int
    {
        return $this->longueurMm;
    }

    public function setLongueurMm(?int $longueurMm): static
    {
        $this->longueurMm = $longueurMm;

        return $this;
    }

    public function getTypeTete(): ?string
    {
        return $this->typeTete;
    }

    public function setTypeTete(?string $typeTete): static
    {
        $this->typeTete = $typeTete;

        return $this;
    }

    public function getTypeEmpreinte(): ?string
    {
        return $this->typeEmpreinte;
    }

    public function setTypeEmpreinte(?string $typeEmpreinte): static
    {
        $this->typeEmpreinte = $typeEmpreinte;

        return $this;
    }

    public function getUnite(): ?string
    {
        return $this->unite;
    }

    public function setUnite(?string $unite): static
    {
        $this->unite = $unite;

        return $this;
    }

    public function getQuantiteConditionnement(): ?int
    {
        return $this->quantiteConditionnement;
    }

    public function setQuantiteConditionnement(?int $quantiteConditionnement): static
    {
        $this->quantiteConditionnement = $quantiteConditionnement;
        
        return $this;
    }
  
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): static
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites->add($favorite);
            $favorite->setProduct($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): static
    {
        if ($this->favorites->removeElement($favorite)) {
            if ($favorite->getProduct() === $this) {
                $favorite->setProduct(null);
            }
        }

        return $this;
    }

}
