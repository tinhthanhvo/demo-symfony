<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product = new Product();
        $product->setName('Ao polo 2');
        $product->setDescription('Ao polo gia re');
        $product->setImage('ao_polo.png');

        $category = new Category();
        $category->setName('Ao');

        $product->setCategory($category);
        $manager->persist($product);
        $manager->flush();
    }
}
