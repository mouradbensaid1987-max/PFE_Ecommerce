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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvents;


class ProductFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $listenerFactory){}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                  'label' => 'Nom'
            ])
            ->add('slug', TextType::class, [
                'label' => 'Slug',
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                  'label' => 'Description', 
                  'required' =>false
            ])
            ->add('priceHt', MoneyType::class, [
                  'label' => 'Prix HT', 
                  'currency' => 'EUR'
            ])
            ->add('stock', IntegerType::class, [
                  'label' => 'Stock'
            ])
            ->add('isActive', CheckboxType::class, [
                  'label' => 'Actif', 
                  'required' => false
            ])
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
            ->add('materiau', TextType::class, [
                  'label' => 'Matériau',
                  'required' => false,
            ])
            ->add('diametreMm', TextType::class, [
                  'label' => 'Diamètre (ex: M6, 4mm, 8mm)',
                  'required' => false,
            ])
            ->add('longueurMm', IntegerType::class, [
                'label' => 'Longueur (mm)',
                'required' => false,
            ])
            ->add('typeTete', TextType::class, [
                'label' => 'Type de tête (ex: Fraisée, Cylindrique, Bombée)',
                'required' => false,
            ])
            ->add('typeEmpreinte', TextType::class, [
                'label' => "Type d'empreinte (ex: Cruciforme PZ2, Torx T20)",
                'required' => false,
            ])
            ->add('unite', ChoiceType::class, [
                'label' => 'Unité de vente',
                'choices' => [
                              'Pièce' => 'piece',
                              'Boîte' => 'boite',
                              'Sachet' => 'sachet',
                              'Kilogramme' => 'kg',
                            ],
                'required' => false,
                'placeholder' => 'Choisir une unité',
            ])
            ->add('quantiteConditionnement', IntegerType::class, [
                'label' => 'Quantité par conditionnement (ex: 100 vis/boîte)',
                'required' => false,
            ])

            ->add('productImages', CollectionType::class, [
                  'entry_type' => ProductImageFormType::class,
                  'by_reference' => false,
                  'entry_options' => ['label' => false],
                  'allow_add' => true,
                  'allow_delete' => true,
                  'attr' => ['class' => 'img-fluid my-2'],
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->listenerFactory->autoslug('name'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->listenerFactory->timestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
