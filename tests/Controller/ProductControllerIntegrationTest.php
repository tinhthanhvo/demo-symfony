<?php

namespace App\Tests\Controller;

use App\DataFixtures\ProductFixtures;
use App\Entity\Category;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerIntegrationTest extends BaseWebTestCase
{
    private $categoryRepository;
    private $productRepository;

    public function setUp(): void
    {
        parent::setUp();

        //Setup client from BaseWebTestCase
        // $client = $this->getApiClient();

        $this->loadFixture(new ProductFixtures());

        $this->categoryRepository = $this->entityManager->getRepository(Category::class);
        $this->productRepository = $this->entityManager->getRepository(Product::class);
    }

    public function testGetProducts()
    {
        $this->client->request(
            Request::METHOD_GET,
            '/api/products',
            [],
            [],
            ['HTTP_ACCEPT' => self::DEFAULT_MIME_TYPE]
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
        $product = $this->productRepository->findOneBy(['name' => 'Product name']);
        $this->client->request(
            Request::METHOD_GET,
            '/api/products/' . $product->getId(),
            [],
            [],
            ['HTTP_ACCEPT' => self::DEFAULT_MIME_TYPE]
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
        $this->assertSame('Product name', $data['name']);
        $this->assertSame('Product description', $data['description']);
    }

    public function testDeleteProduct()
    {
        $product = $this->productRepository->findOneBy(['name' => 'Product name']);
        $this->client->request(
            Request::METHOD_DELETE,
            '/api/products/' . $product->getId(),
            [],
            [],
            ['HTTP_ACCEPT' => self::DEFAULT_MIME_TYPE]
        );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
        $product = $this->productRepository->findOneBy(['name' => 'Product name']);
        $this->assertEmpty($product);
    }
}
