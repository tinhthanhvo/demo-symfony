<?php

namespace App\Tests\Controller;

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

class ProductControllerUnitTest extends TestCase
{
    /**
     * @var ProductRepository|MockObject
     */
    private $productRepository;
    /**
     * @var ProductController|MockObject
     */
    private $productController;

    public function setUp() : void
    {
        $this->productRepository = $this->getMockBuilder(ProductRepository::class)
                                        ->disableOriginalConstructor()
                                        ->getMock();
        $this->productController = $this->getMockBuilder(ProductController::class)
                                        ->onlyMethods(['handleView', 'view', 'createForm'])
                                        ->setConstructorArgs([
                                            $this->productRepository
                                        ])
                                        ->getMock();
    }

    public function testGetProducts(): void
    {
        $product = new Product();
        $view = new View();
        $response = new Response();

        $this->productRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$product]);
        $this->productController->expects($this->once())
            ->method('view')
            ->willReturn($view);
        $this->productController->expects($this->once())
            ->method('handleView')
            ->willReturn($response);

        $result = $this->productController->getProducts();

        $this->assertEquals($response, $result);
    }

    public function testUpdateProduct() : void
    {
        $id = 1;
        $view = new View();
        $response = new Response();
        $product = new Product();

        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()
            ->getMock();
        $this->productRepository->expects($this->once())->method('find')->willReturn($product);
        $form = $this->getMockBuilder(FormInterface::class)->disableOriginalConstructor()
            ->getMock();
        $this->productController->expects($this->once())->method('createForm')->willReturn($form);
        $request->expects($this->once())->method('getContent')->willReturn('');
        $form->expects($this->once())->method('submit');
        $form->expects($this->once())->method('isSubmitted')->willReturn(true);
        $form->expects($this->once())->method('isValid')->willReturn(true);
        $this->productRepository->expects($this->once())->method('add');
        $this->productController->expects($this->once())->method('handleView')->willReturn($response);
        $this->productController->expects($this->once())->method('view')->willReturn($view);

        $this->productController->updateProduct($id, $request);
    }
}
