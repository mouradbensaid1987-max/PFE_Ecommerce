<?php

namespace App\Entity;

use App\Repository\ProductImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity(repositoryClass: ProductImageRepository::class)]
#[Vich\Uploadable]
class ProductImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;


    //mapping: 'product_images' : Pour ce fichier, utilise la configuration product_images définie dans vich_uploader.yaml
    //fileNameProperty: 'imageName' : Après avoir enregistré le fichier, mets son nom dans la propriété imageName.
    #[Vich\UploadableField(mapping: 'product_images', fileNameProperty: 'imageName')]
    #[Assert\Image(
        maxSize: '10M',
        mimeTypes: [
            'image/jpeg',
            'image/png',
            'image/webp'
        ],
        mimeTypesMessage: 'Veuillez sélectionner une image JPEG, PNG ou WebP.',
        maxSizeMessage: 'L image ne doit pas dépasser 10 Mo.'
    )]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'productImages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageFile(): ?File 
    { 
        return $this->imageFile; 
    }

    public function setImageFile(?File $file = null): static
    {
        $this->imageFile = $file;
        if ($file) 
          {
            $this->updatedAt = new \DateTimeImmutable();
          }
        return $this;
    }


    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }
}
