<?php

namespace App\Form;

use App\Entity\CategoryProduct;
use App\Entity\Marque;
use App\Entity\Partner;
use App\Entity\Product;
use App\Entity\SubCategoryProduct;
use App\Form\DataTransformer\FileToStringTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    private $transformer;

    public function __construct(FileToStringTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('product_name', TextType::class, [
                'label_attr'    => ['class' => 'label_required'],
                'label'             => 'Nom du produit',
                'attr'              => [
                    'placeholder'   => '...',
                ]
            ])
            ->add('product_price', MoneyType::class, [
                'label_attr'    => ['class' => 'label_required'],
                'label'             => 'Prix',
                'attr'              => [
                    'placeholder'   => '...',
                ],
                'currency'          => 'XOF',
            ])
            ->add('product_description', TextareaType::class, [
                'label'             => 'Description',
                'attr'              => [
                    'placeholder'   => 'Veuillez saisir la description du produit',
                    'rows'          => 4,
                ],
                "required"          => false
            ])
            ->add('product_size', TextType::class, [
                'label'             => 'Taille(s) disponible',

                'attr'              => [
                    'placeholder'   => 'X,XL,XXL,M',
                ],
                "required"          => false
            ] )
            ->add('quality_product', ChoiceType::class, [
                'label_attr'    => ['class' => 'label_required'],
                'label'             => 'Qualité du produit',
                'choices'           => [
                    'Friperie'             => 'medium_quality',
                    'Haut de gamme'        => 'best_quality',
                ],
                'attr'              => [
                    'placeholder'   => 'Veuillez saisir la description du produit',
                    'class'         =>'browser-default custom-select',
                ],
                "required"          => false
            ])
            ->add('product_color', TextType::class, [
                'label'             => 'Couleur(s) disponible',
                'attr'              => [
                    'placeholder'   => 'Rouge,bleu',
                ],
                "required"          => false
            ])
            ->add('product_reduction', IntegerType::class, [
                'label'             => 'Appliquer une reduction(%)',
                'attr'              => [
                    'max'           => 0,
                ],
                "required"          => false
            ])
            ->add('product_stock', IntegerType::class, [
                'label_attr'    => ['class' => 'label_required'],
                'label'             => 'Stock disponible',
                'attr'              => [
                    'min'           => 0,
                ],
                "required"          => false
            ])
            ->add('category', EntityType::class, [
                'class'             => CategoryProduct::class,
                'choice_value'      => 'id',
                'choice_label'      => 'libelle_fr',
                'label'             => 'Catégorie produit',
                'placeholder'       => 'Veuillez choisir la catégorie',
                'attr'              => [
                    'class'         =>'browser-default custom-select'
                ],
                'label_attr'    => ['class' => 'label_required'],
            ])
            ->add('marque', EntityType::class, [
                'class'             => Marque::class,
                'choice_value'      => 'id',
                'choice_label'      => 'libelle',
                'label'             => 'Choisir la marque',
                'placeholder'       => 'Veuillez choisir la marque',
                'attr'              => [
                    'class'         =>'browser-default custom-select'
                ],
                'required'          => false
            ])->add('partner', EntityType::class, [
                'class'             => Partner::class,
                'choice_value'      => 'id',
                'choice_label'      => 'partner_code',
                'label'             => 'Partner',
                'placeholder'       => 'choisir le partenaire',
                'attr'              => [
                    'class'         =>'browser-default custom-select'
                ],
                'label_attr'    => ['class' => 'label_required'],
            ])
            ->add('sub_category', EntityType::class, [
                'class'             => SubCategoryProduct::class,
                'choice_value'      => 'id',
                'choice_label'      => 'libelle_fr',
                'label'             => 'Sous-catégorie',
                'placeholder'       => 'Sous-catégorie',
                'attr'              => [
                    'class'         =>'browser-default custom-select'
                ],
                'label_attr'    => ['class' => 'label_required'],
            ])
            ->add('product_image', FileType::class, [
                'label_attr'    => ['class' => 'label_required'],
                'label'             => 'Image principale',
                'attr'              => [
                    'placeholder'   => 'Veuillez choisir une image',
                ],
            ])
            ->add('product_image1', FileType::class, [
                'label'             => 'Image 1',
                'attr'              => [
                    'placeholder'   => 'Veuillez choisir une image'
                ],
                "required"          => false
            ])
            ->add('product_image2', FileType::class, [
                'label'             => 'Image 2',
                'attr'              => [
                    'placeholder'   => 'Veuillez choisir une image'
                ],
                "required"          => false
            ])
            ->add('product_image3', FileType::class, [
                'label'             => 'Image',
                'attr'              => [
                    'placeholder'   => 'Veuillez choisir une image'
                ],
                "required"          => false
            ])

        ;
        $builder
            ->get('product_image')->addModelTransformer($this->transformer);
        $builder
            ->get('product_image1')->addModelTransformer($this->transformer);
        $builder
            ->get('product_image2')->addModelTransformer($this->transformer);
        $builder
            ->get('product_image3')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
