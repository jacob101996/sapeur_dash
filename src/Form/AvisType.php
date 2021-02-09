<?php

namespace App\Form;

use App\Entity\Avis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label'         => 'Votre nom',
                'attr'          => [
                    'placeholder'   => 'Nom'
                ]
            ])
            ->add('titre_avis', TextType::class, [
                'label'         => 'Titre commentaire',
                'attr'          => [
                    'placeholder'   => 'Bonne qualité'
                ]
            ] )
            ->add('commentaire', TextareaType::class, [
                'label'         => 'Commentaire',
                'attr'          => [
                    'placeholder'   => "Laissez votre avis ici ...",
                    'rows'      => 5,
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}
