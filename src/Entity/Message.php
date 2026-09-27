<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    public const STATUS_OPEN = 'open';
    public const STATUS_ANSWERED = 'answered';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: 'Le sujet est obligatoire.', normalizer: 'trim')]
    #[Assert\Length(
        min: 3,
        max: 150,
        minMessage: 'Le sujet doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le sujet ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Le message est obligatoire.', normalizer: 'trim')]
    #[Assert\Length(
        min: 10,
        max: 5000,
        minMessage: 'Le message doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le message ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 20)]
    private ?string $status = self::STATUS_OPEN;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        max: 5000,
        maxMessage: 'La réponse ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $reply = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $repliedAt = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

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

    public function getReply(): ?string
    {
        return $this->reply;
    }

    public function setReply(?string $reply): static
    {
        $this->reply = $reply;

        return $this;
    }

    public function getRepliedAt(): ?\DateTimeImmutable
    {
        return $this->repliedAt;
    }

    public function setRepliedAt(?\DateTimeImmutable $repliedAt): static
    {
        $this->repliedAt = $repliedAt;

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
