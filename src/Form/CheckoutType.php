<?php

namespace App\Form;

use App\Entity\Adresse;
use App\Form\AdresseType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CheckoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder


             ->add('typePaiement', ChoiceType::class, [
                'choices' => [
                    'Carte' => 'card',
                    'Prélèvement automatique SEPA' => 'sepa_debit',
                    'Klarna' => 'klarna',
                ],
                'expanded' => true,   // radios au lieu d’un select
                'multiple' => false,
                'mapped' => false, 
                'label' => 'Type de paiement'
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'adresses' => [],
        ]);
    }

}
