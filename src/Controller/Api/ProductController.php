<?php

namespace App\Controller\Api;

use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/products")
     * @param ProductRepository $productRepository
     * @return View
     */
    public function getProducts(ProductRepository $productRepository): View
    {
        $products = $productRepository->findAll();

        return View::create($products, Response::HTTP_OK);
    }
}
