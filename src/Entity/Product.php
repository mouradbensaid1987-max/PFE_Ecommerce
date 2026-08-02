<?php

namespace App\Entity;


use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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
    private ?string $name = null;

    #[ORM\Column(length: 220, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $priceHt = null;

    #[ORM\Column]
    private ?int $stock = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $materiau = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $diametreMm = null;

    #[ORM\Column(nullable: true)]
    private ?int $longueurMm = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $typeTete = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $typeEmpreinte = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $unite = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantiteConditionnement = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
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


    public function __construct()
    {
      $this->createdAt = new \DateTimeImmutable();
      $this->isActive = true;
      $this->productImages = new ArrayCollection();
      $this->orderItems = new ArrayCollection();
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

    public function setName(string $name): static
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

    public function setPriceHt(string $priceHt): static
    {
        $this->priceHt = $priceHt;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
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

}
