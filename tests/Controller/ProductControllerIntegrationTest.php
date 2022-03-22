<?php

namespace App\Tests\Controller;

use App\DataFixtures\ProductFixture;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class ProductControllerIntegrationTest extends BaseWebTestCase
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
        $this->assertSame('Ao polo 2', $product['name']);
        $this->assertSame('Ao polo gia re', $product['description']);
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
        $this->assertSame('Ao polo 2', $product['name']);
        $this->assertSame('Ao polo gia re', $product['description']);
    }

    public function testUpdateProduct()
    {
        $productFixture = new ProductFixture();
        $this->loadFixture($productFixture);
        $productRepository = $this->entityManager->getRepository(Product::class);
        $product = $productRepository->findOneBy(['name' => 'Ao polo 2']);

        $payload = [
            'name' => 'Ao polo update',
            'image' => 'polo.jpg',
            'description' => 'Ao polo gia re',
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