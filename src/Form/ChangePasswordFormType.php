<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Validator\Constraints\Regex;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('plainPassword', RepeatedType::class, [
                            'type' => PasswordType::class,
                            'required' => false,

                            'first_options' => ['label' => 'Nouveau mot de passe'],

                            'second_options' => ['label' => 'Confirmez le mot de passe'],

                            'invalid_message' => 'Les deux mots de passe ne correspondent pas.',

                            'constraints' => [
                                new NotBlank(
                                    message: 'Mot de passe obligatoire.'
                                ),
                                new Length(
                                    min: 6,
                                    minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                                    max: 4096
                                ),
                                new Regex(
                                    pattern: '/[A-Z]/',
                                    message: 'Le mot de passe doit contenir au moins une majuscule.'
                                ),
                                new Regex(
                                    pattern: '/[^A-Za-z0-9]/',
                                    message: 'Le mot de passe doit contenir au moins un caractère spécial (! @ # $ % ...).'
                                ),
                            ],
                    ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
          'data_class' => null,
        ]);
    }
}
