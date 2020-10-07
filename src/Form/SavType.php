<?php

namespace App\Form;

use App\Entity\Sav;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SavType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nfacture')
            ->add('mnt_ttc')
            ->add('ref_cmd')
            ->add('filename')
            ->add('motif')
            ->add('sav_at')
            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sav::class,
        ]);
    }
}
