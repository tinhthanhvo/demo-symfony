<?php

namespace App\Controller\Api;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\FileUploader;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractFOSRestController
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @Rest\Get("/products")
     *
     * @return Response
     */
    public function getProducts(): Response
    {
        $products = $this->productRepository->findAll();

        return $this->handleView($this->view($products));
    }

    /**
     * @Rest\Get("/products/{id}")
     * @param Product $product
     *
     * @return Response:
     */
    public function getProduct(Product $product): Response
    {
        return $this->handleView($this->view($product));
    }

    /**
     * @Rest\Post("/products")
     * @param Request $request
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function insertProduct(Request $request, FileUploader $fileUploader): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->submit($request->request->all());
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadFile = $request->files->get('image');
            if ($uploadFile) {
                $saveFile = $fileUploader->upload($uploadFile);
                $product->setImage($saveFile);
            }
            $this->productRepository->add($product);

            return $this->handleView($this->view($product, Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));
    }
}
