<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Partner;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemandePartenariatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civility', ChoiceType::class, [
                'label'             => 'Civilité',
                'choices'           => [
                    'Monsieur'      => 'Mr.',
                    'Madame'        => 'Mme',
                    'Mademoiselle'  => 'Mlle',
                ],
                'attr'              => [
                    'class'         =>'browser-default custom-select'
                ],
            ])
            ->add('type_room', ChoiceType::class, [
                'label'             => 'Type de pièce ?',
                'choices'           => [
                    "Carte National d'Identité (CNI)"       => 'cni',
                    "Attestaion d'Identité"                 => 'ai',
                    'Carte consulaire'                      => 'cc',
                    "Pasport"                               => 'pp',
                    "Permis de conduire"                    => 'pc'
                ],
                'attr'              => [
                    'class'         =>'browser-default custom-select'
                ],
            ])
            ->add('partner_firstname', TextType::class, [
                "label"         => "Nom de famille",
                "attr"          => [
                    "placeholder" =>  "Nom de famille"
                ]
            ])
            ->add('partner_lastname', TextType::class, [
                "label"         => "Prenom(s)",
                "attr"          => [
                    "placeholder" =>  "Prenom(s)"
                ]
            ])
            ->add('partner_phone', TelType::class, [
                "label"         => "Contact",
                "attr"          => [
                    "placeholder" =>  "Numéro de téléphone"
                ]
            ])
            ->add('partner_shop_name', TextType::class, [
                "label"         => "Raison social",
                "attr"          => [
                    "placeholder" =>  "Nom Entreprise/Boutique"
                ]
            ])
            ->add('partner_nroom', TextType::class, [
                "label"         => "Numéro de la pièce",
                "attr"          => [
                    "placeholder" =>  "Numéro de la pièce"
                ]
            ])
            ->add('partner_residence', TextType::class, [
                "label"         => "Residence",
                "attr"          => [
                    "placeholder" =>  "Ville,commune,quartier"
                ]
            ])
            ->add('partner_fonction', TextType::class, [
                "label"         => "Profession",
                "attr"          => [
                    "placeholder" =>  "Profession"
                ]
            ])
            ->add('datenaissance', DateType::class, [
                "label"         => "Date de naissance",
                "widget"        => "single_text"
            ])
            ->add('contact_enterprise', TelType::class, [
                "label"         => "Contact",
                "attr"          => [
                    "placeholder" =>  "Contact Entreprise/Boutique"
                ]
            ])
            ->add('partner_email', EmailType::class, [
                "label"         => "Adresse mail",
                "attr"          => [
                    "placeholder" =>  "Adresse mail"
                ]
            ])
            ->add('partner_site', TextType::class, [
                "label"         => "Site web/pages",
                "attr"          => [
                    "placeholder" =>  "https://monsite.com"
                ]
            ])
            ->add('siege_social', TextType::class, [
                "label"         => "Siège social",
                "attr"          => [
                    "placeholder" =>  "Où est situé l'entreprise/la boutique ?"
                ]
            ])
            ->add('activity', EntityType::class, [
                "label"         => "Votre activité",
                "class"         => Activity::class,
                "choice_label"  => 'libelle',
                "choice_value"  => 'id',
                "attr"          => [
                    "placeholder" =>  "Activité",
                    'class'         =>'browser-default custom-select'
                ],
                "required"      => true
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
