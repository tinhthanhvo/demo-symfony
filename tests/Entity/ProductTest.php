<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\Price;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product = new Product();
    }

    public function testGetName()
    {
        $value = 'Product name';

        $response = $this->product->setName($value);

        self::assertInstanceOf(Product::class, $response);
        self::assertEquals($value, $this->product->getName());
    }

    public function testGetImage()
    {
        $value = 'product_image.jpg';

        $response = $this->product->setImage($value);

        self::assertInstanceOf(Product::class, $response);
        self::assertEquals($value, $this->product->getImage());
    }

    public function testGetDescription()
    {
        $value = 'Product description ...';

        $response = $this->product->setDescription($value);

        self::assertInstanceOf(Product::class, $response);
        self::assertEquals($value, $this->product->getDescription());
    }

    public function testGetCategory()
    {
        $value = new Category();

        $response = $this->product->setCategory($value);

        self::assertInstanceOf(Product::class, $response);
        self::assertEquals($value, $this->product->getCategory());
    }

    public function testGetPrices()
    {
        $value = new Price();

        $response = $this->product->addPrice($value);

        self::assertInstanceOf(Product::class, $response);
        self::assertCount(1, $this->product->getPrices());
        self::assertTrue($this->product->getPrices()->contains($value));

        $response = $this->product->removePrice($value);

        self::assertInstanceOf(Product::class, $response);
        self::assertCount(0, $this->product->getPrices());
        self::assertFalse($this->product->getPrices()->contains($value));
    }
}
