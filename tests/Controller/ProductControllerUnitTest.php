<?php

namespace App\Tests\Controller;

use App\Controller\Api\ProductController;
use App\Entity\Product;
use App\Repository\ProductRepository;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializationContext;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerUnitTest extends TestCase
{
    /**
     * @var ProductController|MockObject
     */
    private $productController;

    /**
     * @var ProductRepository|MockObject
     */
    private $productRepository;

    /**
     * @var SerializerBuilder
     */
    private $builder;

    public function setUp(): void
    {
        $this->productRepository = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->productController = $this->getMockBuilder(ProductController::class)
            ->onlyMethods(['handleView', 'view'])
            ->setConstructorArgs([$this->productRepository])
            ->getMock();

        $this->builder = SerializerBuilder::create();
    }

    public function testGetProducts(): void
    {
        $product = new Product();
        $this->productRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$product]);

        $serializer = $this->builder->build();
        $this->assertEquals('"text"', $serializer->serialize('text', 'json'));
        $this->assertEquals('text', $serializer->deserialize('"text"', 'string', 'json'));

        $view = new View();
        $this->productController->expects($this->once())
            ->method('view')
            ->willReturn($view);

        $response = new Response();
        $this->productController->expects($this->once())
            ->method('handleView')
            ->willReturn($response);

        $result = $this->productController->getProducts();
        $this->assertEquals($response, $result);
    }

    public function testGetProduct(): void
    {
        $serializer = $this->builder->build();
        $this->assertEquals('"text"', $serializer->serialize('text', 'json'));
        $this->assertEquals('text', $serializer->deserialize('"text"', 'string', 'json'));

        $view = new View();
        $this->productController->expects($this->once())
            ->method('view')
            ->willReturn($view);

        $response = new Response();
        $this->productController->expects($this->once())
            ->method('handleView')
            ->willReturn($response);

        $product = new Product();
        $result = $this->productController->getProduct($product);
        $this->assertEquals($response, $result);
    }

    public function testDeleteProduct(): void
    {
        $product = new Product();
        $this->productRepository->expects($this->once())
            ->method('find')
            ->willReturn($product);

        $view = new View();
        $this->productController->expects($this->once())
            ->method('view')
            ->willReturn($view);

        $response = new Response();
        $this->productController->expects($this->once())
            ->method('handleView')
            ->willReturn($response);

        $this->productRepository->expects($this->once())
            ->method('remove');

        $id = 1;
        $this->productController->deleteProduct($id);
    }
}
