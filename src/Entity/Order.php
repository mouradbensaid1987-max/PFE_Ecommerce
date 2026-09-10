<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{

    //public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    //public const STATUS_CANCELLED = 'cancelled';


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30, unique: true)]
    private ?string $reference = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 20)]
    private ?string $status = self::STATUS_PAID;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalTtc = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $shippingCost = null;

    #[ORM\Column(length: 100)]
    private ?string $deliveryFirstName = null;

    #[ORM\Column(length: 100)]
    private ?string $deliveryLastName = null;

    #[ORM\Column(length: 255)]
    private ?string $deliveryStreet = null;

    #[ORM\Column(length: 20)]
    private ?string $deliveryPostalCode = null;

    #[ORM\Column(length: 100)]
    private ?string $deliveryCity = null;

    #[ORM\Column(length: 100)]
    private ?string $deliveryCountry = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

  

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'orderRef', cascade: ['persist', 'remove'])]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->reference = 'CMD-' . strtoupper(bin2hex(random_bytes(4)));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTotalTtc(): ?string
    {
        return $this->totalTtc;
    }

    public function setTotalTtc(string $totalTtc): static
    {
        $this->totalTtc = $totalTtc;

        return $this;
    }
    public function getShippingCost(): ?string
    {
        return $this->shippingCost;
    }

    
    public function setShippingCost(string $shippingCost): static
    {
        $this->shippingCost = $shippingCost;

        return $this;
    }

    public function getDeliveryFirstName(): ?string
    {
        return $this->deliveryFirstName;
    }

    public function setDeliveryFirstName(string $deliveryFirstName): static
    {
        $this->deliveryFirstName = $deliveryFirstName;

        return $this;
    }

    public function getDeliveryLastName(): ?string
    {
        return $this->deliveryLastName;
    }

    public function setDeliveryLastName(string $deliveryLastName): static
    {
        $this->deliveryLastName = $deliveryLastName;

        return $this;
    }

    public function getDeliveryStreet(): ?string
    {
        return $this->deliveryStreet;
    }

    public function setDeliveryStreet(string $deliveryStreet): static
    {
        $this->deliveryStreet = $deliveryStreet;

        return $this;
    }

    public function getDeliveryPostalCode(): ?string
    {
        return $this->deliveryPostalCode;
    }

    public function setDeliveryPostalCode(string $deliveryPostalCode): static
    {
        $this->deliveryPostalCode = $deliveryPostalCode;

        return $this;
    }

    public function getDeliveryCity(): ?string
    {
        return $this->deliveryCity;
    }

    public function setDeliveryCity(string $deliveryCity): static
    {
        $this->deliveryCity = $deliveryCity;

        return $this;
    }

    public function getDeliveryCountry(): ?string
    {
        return $this->deliveryCountry;
    }

    public function setDeliveryCountry(string $deliveryCountry): static
    {
        $this->deliveryCountry = $deliveryCountry;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setOrderRef($this);
        }

        return $this;
    }

    public function removeItem(OrderItem $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getOrderRef() === $this) {
                $item->setOrderRef(null);
            }
        }

        return $this;
    }
}
