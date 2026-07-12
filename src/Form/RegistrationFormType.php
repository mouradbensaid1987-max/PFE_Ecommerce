<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [new NotBlank()],
                ])


            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'constraints' => [new NotBlank()],
                ])


            ->add('email', EmailType::class, [
                'label' => 'Email',
                ])


            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => "J'accepte les conditions",
                'constraints' => [
                    new IsTrue(
                        message: 'Vous devez accepter les conditions.')],
                ])


            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Mot de passe',
                'constraints' => [
                    new NotBlank(
                        message: 'Mot de passe obligatoire'),
                    new Length(
                        min: 6,
                        minMessage: 'Au moins 6 caractères'),
                    ],
                ]);

                
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
