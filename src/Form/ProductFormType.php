<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Tva;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('slug', TextType::class, ['label' => 'Slug'])
            ->add('description', TextareaType::class, ['label' => 'Description', 'required' =>false])
            ->add('priceHt', MoneyType::class, ['label' => 'Prix HT', 'currency' => 'EUR'])
            ->add('stock', IntegerType::class, ['label' => 'Stock'])
            ->add('isActive', CheckboxType::class, ['label' => 'Actif', 'required' => false])
            
            ->add('category', EntityType::class, [
                  'class' => Category::class,
                  'choice_label' => 'name',
                  'label' => 'Catégorie'
            ])
            ->add('tva', EntityType::class, [
                  'class' => Tva::class,
                  'choice_label' => 'name',
                  'label' => 'TVA'
            ])
            ->add('productImages', CollectionType::class, [
                  'entry_type' => ProductImageFormType::class,
                  'allow_add' => true,
                  'allow_delete' => true,
                  'by_reference' => false,
                  'label' => false,
                  'attr' => ['class' => 'img-fluid my-2'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
