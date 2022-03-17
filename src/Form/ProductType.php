<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

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
            ->add('category', ChoiceType::class, [
                'choices' => $this->categoryRepository->findAll(),
                'choice_value' => 'name',
                'choice_label' => function (?Category $category) {
                    return $category ? $category->getName() : 'Category data not found';
                },
            ])
            ->add('save', SubmitType::class, ['label' => 'Submit'])
            ->add('cancel', ButtonType::class, [
                'label' => 'Cancel',
                'attr' => ['class' => 'form-button form-control form-control-lg btn-light'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
