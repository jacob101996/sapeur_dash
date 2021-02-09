<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                "label"     => "Nom d'utilisateur",
                "attr"      => [
                    "placeholder"   => "Nom d'utilisateur"
                ]
            ])
            ->add('roles', ChoiceType::class, [
                "label"     => "Rôle de l'utilisateur",
                "attr"      => [
                    'class'         =>'browser-default custom-select'
                ],
                "choices"           => [
                    'Partenaire'    => 'ROLE_PARTNER',
                    'Administrateur'=> 'ROLE_ADMIN'
                ],
                "mapped"            => false
            ])
            ->add('email', EmailType::class, [
                "label"     => "Adresse email",
                "attr"      => [
                    "placeholder"   => "..."
                ]
            ])
            ->add('password', RepeatedType::class, [
                'invalid_message'   => 'Les deux mots de passe ne correspondent pas',
                'required'          => true,
                'type'              => PasswordType::class,
                'first_options'     => ['label' => 'Mot de passe'],
                'second_options'    => ['label' => 'Confrmez le mot de passe'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
