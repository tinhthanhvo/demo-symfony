<?php

namespace App\Controller\Api;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
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
     * @return Response
     */
    public function getProduct(Product $product): Response
    {
        return $this->handleView($this->view($product));
    }

    /**
     * @Rest\Post("/products")
     * @param Request $request
     *
     * @return Response
     */
    public function insertProduct(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->submit($request->request->all());
        if ($form->isSubmitted() && $form->isValid()) {
            $this->productRepository->add($product);

            return $this->handleView($this->view(null, Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors()));
    }

    /**
     * @Rest\Put("/products/{id}")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function updateProduct(int $id, Request $request, FileUploader $fileUploader): Response
    {
        $product = $this->productRepository->find($id);
        if(!$product) {
            $view = $this->view(['error' => 'Product is not found.'], Response::HTTP_NOT_FOUND);
            return $this->handleView($view);
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->submit($request->request->all());
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadFile = $request->files->get('image');
            if ($uploadFile) {
                $saveFile = $fileUploader->upload($uploadFile);
                $product->setImage($saveFile);
            }
            $this->productRepository->add($product);

            return $this->handleView($this->view($product, Response::HTTP_OK));
        }

        return $this->handleView($this->view($form->getErrors()));
    }
}
