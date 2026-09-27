<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{



    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire.')]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'Le prénom doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: "/^[\p{L}\s'\-]+$/u",
        message: 'Le prénom contient des caractères non autorisés.'
    )]
    private ?string $firstName = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: "/^[\p{L}\s'\-]+$/u",
        message: 'Le nom contient des caractères non autorisés.'
    )]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'L\'adresse est obligatoire.')]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'L\'adresse doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'L\'adresse ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $street = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'Le code postal est obligatoire.')]
    #[Assert\Length(
        max: 20,
        maxMessage: 'Le code postal ne peut pas dépasser {{ limit }} caractères.'
    )]
     #[Assert\Regex(
        pattern: '/^\d{5}$/',
        message: 'Le code postal doit contenir exactement 5 chiffres.'
    )]
    private ?string $postalCode = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'La ville est obligatoire.')]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'La ville doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'La ville ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $city = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le pays est obligatoire.')]
    #[Assert\Length(max: 100)]
    private ?string $country = 'France';

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\Length(
        max: 30,
        maxMessage: 'Le numéro de téléphone ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '/^\+?[0-9\s.\-()]{6,30}$/',
        message: 'Le numéro de téléphone n\'est pas valide.'
    )]
    private ?string $phone = null;


    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

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

}
