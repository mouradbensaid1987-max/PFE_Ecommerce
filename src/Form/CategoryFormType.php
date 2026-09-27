<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;



class CategoryFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $listenerFactory){}
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom', 'required' => false])
            ->add('slug', HiddenType::class)
            ->add('description', TextareaType::class, ['label' => 'Description', 'required' => false])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->listenerFactory->autoslug('name'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
