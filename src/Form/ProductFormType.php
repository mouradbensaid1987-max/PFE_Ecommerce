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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


class ProductFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $listenerFactory){}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                  'label' => 'Nom',
                  'required' => false,
            ])
            ->add('slug', HiddenType::class)

            ->add('description', TextareaType::class, [
                  'label' => 'Description', 
                  'required' =>false
            ])
            ->add('priceHt', MoneyType::class, [
                  'label' => 'Prix HT', 
                  'currency' => 'EUR',
                  'required' => false,
          
            ])
            ->add('stock', IntegerType::class, [
                  'label' => 'Stock',
                  'required' => false,
        
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
                  'label' => 'Diamètre',
                  'attr' => ['placeholder' => '(ex: M6, 4mm, 8mm)'],
                  'required' => false,
            ])
            ->add('longueurMm', IntegerType::class, [
                'label' => 'Longueur',
                'attr' => ['placeholder' => 'mm'],
                'required' => false,
            ])
            ->add('typeTete', TextType::class, [
                'label' => 'Type de tête',
                'attr' => ['placeholder' => '(ex: Fraisée, Cylindrique, Bombée)'],
                'required' => false,
            ])
            ->add('typeEmpreinte', TextType::class, [
                'label' => "Type d'empreinte",
                'attr' => ['placeholder' => '(ex: Cruciforme PZ2, Torx T20)'],
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
                'label' => 'Unités par boîte',
                'required' => false,
            ])

            ->add('productImages', CollectionType::class, [ //CollectionType permet de gérer plusieurs champs du même type.
                  'entry_type' => ProductImageFormType::class, //Chaque élément de ma collection productImages doit utiliser le formulaire ProductImageFormType.
                  'by_reference' => false, // Symfony utilise les méthodes de ton entité pour ajouter ou supprimer les éléments.
                  'entry_options' => ['label' => false],
                  'allow_add' => true, // tres important pour cree le prototype 
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
