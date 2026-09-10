<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cette adresse e-mail.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 180)]
    #[Assert\Email(message: 'L’adresse e-mail "{{ value }}" n’est pas valide.')]
    #[Assert\Length(
        max: 120,
        maxMessage: 'L’adresse e-mail ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $email = null;

  
    #[ORM\Column]
    private array $roles = [];


    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Regex(
        pattern: "/^[\p{L}\s'-]+$/u",
        message: 'Le nom ne peut contenir que des lettres, des espaces, des apostrophes et des tirets.'
    )]
    private ?string $firstName = null;


    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\NotBlank(message: 'Le prenom est obligatoire.')]
    #[Assert\Regex(
        pattern: "/^[\p{L}\s'-]+$/u",
        message: 'Le prenom ne peut contenir que des lettres, des espaces, des apostrophes et des tirets.'
    )]
    private ?string $lastName = null;


    #[ORM\Column]
    private bool $isVerified = false;

  
    #[ORM\Column]
    private ?string $password = null;

  
    #[ORM\OneToMany(targetEntity: Address::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $addresses;

  
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'user')]
    private Collection $orders;

    
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'user')]
    private Collection $messages;

    /**
     * @var Collection<int, ResetPasswordRequest>
     */
    #[ORM\OneToMany(targetEntity: ResetPasswordRequest::class, mappedBy: 'user')]
    private Collection $resetPasswordRequests;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->resetPasswordRequests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    
    public function isVerified(): bool
    {
        return $this->isVerified;
    }


    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

  
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

  
    

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);
        
        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
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

  
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): static
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setUser($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): static
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getUser() === $this) {
                $address->setUser(null);
            }
        }

        return $this;
    }

  
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

  
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setUser($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getUser() === $this) {
                $message->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ResetPasswordRequest>
     */
    public function getResetPasswordRequests(): Collection
    {
        return $this->resetPasswordRequests;
    }

    public function addResetPasswordRequest(ResetPasswordRequest $resetPasswordRequest): static
    {
        if (!$this->resetPasswordRequests->contains($resetPasswordRequest)) {
            $this->resetPasswordRequests->add($resetPasswordRequest);
            $resetPasswordRequest->setUser($this);
        }

        return $this;
    }

    public function removeResetPasswordRequest(ResetPasswordRequest $resetPasswordRequest): static
    {
        if ($this->resetPasswordRequests->removeElement($resetPasswordRequest)) {
            // set the owning side to null (unless already changed)
            if ($resetPasswordRequest->getUser() === $this) {
                $resetPasswordRequest->setUser(null);
            }
        }

        return $this;
    }

    
    
}
