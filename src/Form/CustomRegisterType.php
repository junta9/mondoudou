<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class CustomRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'label' => 'E-mail',
            'required' => true,
            'attr' => [
                'required' => 'required',
                'class' => 'inputBox'
            ]
        ])
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de passe doivent correspondre.',
            'required' => true,
            'first_options' => [
                'label' => 'Mot de passe',
                'attr' => [
                    'class' => 'myPass'
                ],
                // 'constraints' => [
                //     new Regex('/^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{8,}$/', 
                //     "Il faut un mot de passe de 8 caractères avec 1 majuscule, 1 miniscule, 1 chiffre et 1 caractère spécial")
                // ]
            ],
            'second_options' => [
                'label' => 'Répéter le mot de passe',
                'attr' => [
                    'class' => 'myPass'
                ],
                // 'constraints' => [
                //     new Regex('/^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{8,}$/', 
                //     "Il faut un mot de passe de 8 caractères avec 1 majuscule, 1 miniscule, 1 chiffre et 1 caractère spécial")
                // ]
            ]
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Envoyer',
            'attr' => [
                'class' => 'btn btn-dark'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
