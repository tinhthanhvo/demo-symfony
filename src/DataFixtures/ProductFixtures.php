<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $product = new Product();
        $product->setName('Product name');
        $product->setDescription('Product description');
        $product->setImage('product_image.png');

        $category = new Category();
        $category->setName('Category name');

        $product->setCategory($category);
        $manager->persist($product);
        $manager->flush();
    }
}
