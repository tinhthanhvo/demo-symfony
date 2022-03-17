<?php

namespace App\Controller\Api;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
//use FOS\RestBundle\View\View;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractFOSRestController
{

    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @Rest\Get("/products")
     * @return View
     */
    public function getProducts(): View
    {
        $products = $this->productRepository->findAll();

        return View::create($products, Response::HTTP_OK);
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
