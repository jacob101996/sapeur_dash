<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Civility;
use App\Entity\Partner;
use App\Entity\TypeRoom;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civility', EntityType::class, [
                'label_attr' => ['class' => 'label_required'],
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
                'label_attr' => ['class' => 'label_required'],
                "label"         => "Type de pièce",
                "placeholder"   => "...",
                "class"         => TypeRoom::class,
                "choice_label"  => "libelle_fr",
                "choice_value"  => "id",
                'attr'              => [
                    'class'         =>'browser-default custom-select'
                ]
            ])
            ->add('partner_email', EmailType::class, [
                "label"     => "Email",
                "attr"      => [
                    "placeholder"   => "..."
                ],
                'required'          => false
            ])
            ->add('partner_site', TextType::class, [
                "label"     => "Site web",
                "attr"      => [
                    "placeholder"   => "..."
                ],
                'required'          => false
            ])
            ->add('siege_social', TextType::class, [
                'label_attr' => ['class' => 'label_required'],
                "label"     => "Siege Social",
                "attr"      => [
                    "placeholder"   => "..."
                ]
            ])
            ->add('activity', EntityType::class, [
                'label_attr' => ['class' => 'label_required'],
                "label"         => "Activité",
                "placeholder"   => "...",
                "class"         => Activity::class,
                "choice_label"  => "libelle",
                "choice_value"  => "id",
                'attr'              => [
                    'class'         =>'browser-default custom-select'
                ]
            ])
            ->add('partner_firstname', TextType::class, [
                'label_attr' => ['class' => 'label_required'],
                "label"     => "Nom",
                "attr"      => [
                    "placeholder"   => "Nom de famille"
                ]
            ])
            ->add('partner_lastname', TextType::class, [
                'label_attr' => ['class' => 'label_required'],
                "label"     => "Prenom(s)",
                "attr"      => [
                    "placeholder"   => "..."
                ]
            ])
            ->add('partner_phone', TelType::class, [
                'label_attr' => ['class' => 'label_required'],
                "label"     => "Telephone",
                "attr"      => [
                    "placeholder"   => "..."
                ]
            ])
            ->add('partner_shop_name', TextType::class, [
                'label_attr' => ['class' => 'label_required'],
                "label"     => "Nom de la boutique",
                "attr"      => [
                    "placeholder"   => "..."
                ]
            ])
            ->add('partner_nroom', TextType::class, [
                'label_attr' => ['class' => 'label_required'],
                "label"     => "N° Pièce",
                "attr"      => [
                    "placeholder"   => "..."
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Partner::class,
        ]);
    }
}
