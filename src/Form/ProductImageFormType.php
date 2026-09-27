<?php

namespace App\Form;


use App\Entity\ProductImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductImageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('imageFile', VichImageType::class, [ //Cela indique que tu utilises le type de formulaire fourni par VichUploaderBundle.
                      'required' => false,
                      'allow_delete' => false, // L'utilisateur ne peut pas supprimer directement l'image existante avec une case « supprimer ».
                      'download_uri' => false, //n'affiche pas le lien de téléchargement pour cette image.
                      'label' => false,
                      'image_uri' => false, // Ne pas afficher automatiquement l'image actuelle dans ce champ.
                      'attr' => ['class' => 'mb-3'],
                      ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductImage::class,
        ]);
    }
}
