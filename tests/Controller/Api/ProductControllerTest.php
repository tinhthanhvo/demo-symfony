<?php

namespace App\Tests\Controller\Api;

use App\Controller\Api\ProductController;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\FileUploader;
use FOS\RestBundle\View\View;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends TestCase
{
    /**
     * @var ProductRepository|MockObject
     */
    private $productRepository;

    /**
     * @var ProductController|MockObject
     */
    private $productController;

    protected function setUp(): void
    {
        $this->productRepository = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()->getMock();
        $this->productController = $this->getMockBuilder(ProductController::class)
            ->onlyMethods(['handleView', 'view', 'createForm'])
            ->setConstructorArgs([$this->productRepository])
            ->getMock();
    }

    /**
     * @test
     * @covers \App\Controller\Api\ProductController::getProducts
     */
    public function testGetProducts()
    {
        $product = new Product();
        $response = new Response();
        $view = new View();

        $this->productRepository->expects($this->once())->method('findAll')
            ->willReturn([$product]);
        $this->productController->expects($this->once())->method('handleView')
            ->willReturn($response);
        $this->productController->expects($this->once())->method('view')
            ->willReturn($view);

        $result = $this->productController->getProducts();

        $this->assertEquals($response, $result);
    }

    /**
     * @test
     * @covers \App\Controller\Api\ProductController::getProduct
     */
    public function testGetProduct()
    {
        $product = new Product();
        $response = new Response();
        $view = new View();
        $this->productController->expects($this->once())->method('handleView')
            ->willReturn($response);
        $this->productController->expects($this->once())->method('view')
            ->willReturn($view);

        $this->productController->getProduct($product);
    }

    public function testInsertProduct()
    {
        $response = new Response();
        $view = new View();

        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()
            ->getMock();
        $fileUploader = $this->getMockBuilder(FileUploader::class)->disableOriginalConstructor()
            ->getMock();
        $form = $this->getMockBuilder(FormInterface::class)->disableOriginalConstructor()
            ->getMock();
        $this->productController->expects($this->once())->method('createForm')->willReturn($form);
        $request->expects($this->once())->method('getContent')->willReturn('');
        $form->expects($this->once())->method('submit');
        $form->expects($this->once())->method('isSubmitted')->willReturn(true);
        $form->expects($this->once())->method('isValid')->willReturn(true);

        $fileBag = $this->getMockBuilder(FileBag::class)->disableOriginalConstructor()
            ->getMock();
        $uploadFile = $this->getMockBuilder(UploadedFile::class)->disableOriginalConstructor()
            ->getMock();
        $request->files = $fileBag;

        $fileBag->expects($this->once())->method('get')->willReturn($uploadFile);
        $fileUploader->expects($this->once())->method('upload')->willReturn('');
        $this->productRepository->expects($this->once())->method('add');
        $this->productController->expects($this->once())->method('handleView')->willReturn($response);
        $this->productController->expects($this->once())->method('view')->willReturn($view);

        $this->productController->insertProduct($request, $fileUploader);
    }
}
