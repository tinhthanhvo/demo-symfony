<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $category = new Category();
        $category->setName('Category name');

        $product = new Product();
        $product->setName('Product name');
        $product->setImage('Product image');
        $product->setDescription('Product description');
        $product->setCategory($category);

        $manager->persist($product);
        $manager->flush();
    }
}
