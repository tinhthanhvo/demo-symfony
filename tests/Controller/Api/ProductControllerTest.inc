<?php

namespace App\Tests\Controller\Api;

use App\DataFixtures\ProductFixture;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class ProductControllerTest extends BaseWebTestCase
{
    use ReloadDatabaseTrait;
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testGetProducts()
    {
        $productFixture = new ProductFixture();
        $this->loadFixture($productFixture);

        $this->client->request(
            Request::METHOD_GET,
            '/api/products',
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/json',
            ]
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $product = $data[0];
        $this->assertSame('Product name', $product['name']);
        $this->assertSame('Product description', $product['description']);
    }

    public function testGetProduct()
    {
        $productFixture = new ProductFixture();
        $this->loadFixture($productFixture);
        $productRepository = $this->entityManager->getRepository(Product::class);
        $product = $productRepository->findOneBy(['name' => 'Ao polo 2']);

        $this->client->request(
            Request::METHOD_GET,
            '/api/products/'.$product->getId(),
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/json',
            ]
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $product = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('Product name', $product['name']);
        $this->assertSame('Product description', $product['description']);
    }

}