<?php

namespace App\Tests\Controller\Api;

use App\DataFixtures\CategoryFixtures;
use App\DataFixtures\ProductFixtures;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Controller\Api\BaseWebTestCase;

class ProductControllerIntegrationTest extends BaseWebTestCase
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
            ['HTTP_ACCEPT' => 'application/json']
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
            ['HTTP_ACCEPT' => 'application/json']
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('Product name', $data['name']);
        $this->assertSame('Product description', $data['description']);
    }

    public function testInsertProduct()
    {
        $categoryFixture = new CategoryFixtures();
        $this->loadFixture($categoryFixture);

        $payload = [
            'name' => 'Product name',
            'description' => 'Product description',
            'category' => 1,
            'image' => 'product_image.png',
        ];

        $this->client->request(
            Request::METHOD_POST,
            '/api/products',
            [json_encode($payload)],
            [],
            [
                'HTTP_ACCEPT' => 'application/json',
            ],
            json_encode($payload)
        );

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $productRepository = $this->entityManager->getRepository(Product::class);
        $product = $productRepository->findOneBy(['name' => 'Product name']);
        $this->assertNotEmpty($product);
        $this->assertSame('Product name', $product->getName());
    }
}