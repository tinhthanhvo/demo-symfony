<?php

namespace App\Tests\Controller\Api;

use App\DataFixtures\ProductFixtures;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use App\Tests\Controller\BaseWebTestCase;

class ProductControllerTest extends BaseWebTestCase
{
    use ReloadDatabaseTrait;
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testGetProducts()
    {
        $productFixture = new ProductFixtures();
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
        $productFixture = new ProductFixtures();
        $this->loadFixture($productFixture);
        $productRepository = $this->entityManager->getRepository(Product::class);
        $product = $productRepository->findOneBy(['name' => 'Product name']);

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

    public function testUpdateProduct()
    {
        $productFixture = new ProductFixtures();
        $this->loadFixture($productFixture);
        $productRepository = $this->entityManager->getRepository(Product::class);
        $product = $productRepository->findOneBy(['name' => 'Product name']);

        $payload = [
            'name' => 'Product name update',
            'image' => 'product_image.jpg',
            'description' => 'Product description',
            'category' => 1
        ];
        $this->client->request(
            Request::METHOD_PUT,
            '/api/products/'.$product->getId(),
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/json',
            ],
            json_encode($payload)
        );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
        $this->entityManager->refresh($product);
        $product = $productRepository->find($product->getId());
        $this->assertIsObject($product);
        $this->assertSame($payload['name'], $product->getName());
        $this->assertSame($payload['description'], $product->getDescription());
        $this->assertIsObject($product->getCategory());
    }
}