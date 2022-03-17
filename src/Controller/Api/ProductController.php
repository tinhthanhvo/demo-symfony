<?php

namespace App\Controller\Api;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
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
     */
    public function getProducts(): Response
    {
        $products = $this->productRepository->findAll();

        return $this->handleView($this->view($products));
    }

    /**
     * @Rest\Get("/products/{id}")
     * @param $id
     * @return View
     */
    public function getProduct($id): View
    {
        $product = $this->productRepository->find($id);

        return View::create($product, Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/products")
     * @param Request $request
     * @param CategoryRepository $categoryRepository
     * @return View
     */
    public function insertProduct(Request $request, CategoryRepository $categoryRepository): View
    {
        $data = json_decode($request->getContent(), true);
        $product = new Product();
        $category = $categoryRepository->find($data['category_id']);

        $form = $this->createForm(ProductType::class, $product);
        $form->submit($data);
        $product->setCategory($category);
        $this->productRepository->add($product);

        return View::create($product, Response::HTTP_CREATED);
    }
}
