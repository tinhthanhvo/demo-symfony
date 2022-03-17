<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Product name'])
            ->add('description', TextareaType::class, ['label' => 'Description'])
//            ->add('image', FileType::class, [
//                'label' => 'Image (JPG, JPEG, PNG or SVG file)',
//                'mapped' => false,
//                'required' => false,
//                'constraints' => [
//                    new File([
//                        'maxSize' => '5M',
//                        'mimeTypes' => [
//                            'image/jpg',
//                            'image/jpeg',
//                            'image/png',
//                            'image/svg+xml',
//                        ],
//                        'maxSizeMessage' => 'File is too large.',
//                        'mimeTypesMessage' => 'Please upload a valid Image file.',
//                    ])
//                ],
//            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'csrf_protection' => false,
        ]);
    }
}
