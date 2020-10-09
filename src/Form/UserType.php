<?php

namespace App\Form;

use App\Entity\Civility;
use App\Entity\TypeRoom;
use App\Entity\User;
use App\Form\DataTransformer\FileToStringTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
                'second_options'    => ['label' => 'Confrmez'],
            ])
            ->add('partner_firstname', TextType::class, [
                "label"     => "Nom",
                "attr"      => [
                    "placeholder"   => "Nom de famille"
                ]
            ])
            ->add('partner_lastname', TextType::class, [
                "label"     => "Prenom(s)",
                "attr"      => [
                    "placeholder"   => "..."
                ]
            ])
            ->add('partner_phone', TelType::class, [
                "label"     => "Telephone",
                "attr"      => [
                    "placeholder"   => "..."
                ]
            ])
            ->add('partner_shop_name', TextType::class, [
                "label"     => "Nom de la boutique",
                "attr"      => [
                    "placeholder"   => "..."
                ]
            ])
            ->add('partner_nroom', TextType::class, [
                "label"     => "N° Pièce",
                "attr"      => [
                    "placeholder"   => "..."
                ]
            ])
            ->add('partner_residence', TextType::class, [
                "label"     => "Lieu d'habitation",
                "attr"      => [
                    "placeholder"   => "..."
                ]
            ])
            ->add('civility', EntityType::class, [
                "label"         => "Civilité",
                "placeholder"   => "...",
                "class"         => Civility::class,
                "choice_label"  => "libelle",
                "choice_value"  => "id",
                'attr'              => [
                    'class'         =>'browser-default custom-select'
                ]
            ])
            ->add('type_room', EntityType::class, [
                "label"         => "Type de pièce",
                "placeholder"   => "...",
                "class"         => TypeRoom::class,
                "choice_label"  => "libelle_fr",
                "choice_value"  => "id",
                'attr'              => [
                    'class'         =>'browser-default custom-select'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => User::class,]);
    }
}
