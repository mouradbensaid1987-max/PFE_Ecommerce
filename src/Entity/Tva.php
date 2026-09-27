<?php

namespace App\Entity;

use App\Repository\TvaRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TvaRepository::class)]
#[UniqueEntity(fields: 'name', message: 'Ce nom de TVA est déjà utilisé.')]
class Tva
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le nom de la TVA est obligatoire.')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'tva', targetEntity: Product::class)]
    private Collection $products;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    #[Assert\NotBlank(message: 'Le taux de TVA est obligatoire.')]
    #[Assert\Range(
        min: 0,
        max: 100,
        notInRangeMessage: 'Le taux doit être compris entre {{ min }} et {{ max }} %.'
    )]
    #[Assert\Regex(
        pattern: '/^\d{1,3}(\.\d{1,2})?$/',
        message: 'Le taux doit avoir au maximum 2 décimales (ex : 20 ou 5.50).'
    )]
    private ?string $rate = null;

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

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function setRate(?string $rate): static
    {
        $this->rate = $rate;

        return $this;
    }
    public function __toString(): string
    {
    return $this->name . ' (' . $this->rate . '%)';
    }
    public function getProducts(): Collection
    {
        return $this->products;
    }

    
    public function setProducts(Collection $products)
    {
        $this->products = $products;

        return $this;
    }
    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setTva($this);
        }

        return $this;
    }
}
