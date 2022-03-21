<?php

namespace App\Form;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CategoryType extends AbstractType
{
    /** @var CategoryRepository */
    private $categoryRepository;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('parent', ChoiceType::class, [
                'choices' => $this->getCategoryParentList(),
                'choice_value' => 'name',
                'choice_label' => function (?Category $category) {
                    return ($category) ? $category->getName() : 'Category data not found';
                },
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                if ($data['parent'] == '') {
                    $event->setData([
                        'name' => $data['name'],
                        'parent' => $this->getCategoryParentDefault()->getName(),
                    ]);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }

    /**
    * @return CategoryRepository
    */
    public function getCategoryRepository(): CategoryRepository
    {
        return $this->categoryRepository;
    }

    /**
    * @param CategoryRepository $categoryRepository
    * @return void
    */
    public function setCategoryRepository(CategoryRepository $categoryRepository): void
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return array
     */
    public function getCategoryParentList(): array
    {
        return array_merge(array(new Category()), $this->categoryRepository->findAll());
    }

    /**
     * @return Category
     */
    public function getCategoryParentDefault(): Category
    {
        return $this->categoryRepository->find(1);
    }
}
