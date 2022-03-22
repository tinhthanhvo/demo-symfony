<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/product", name="product_index")
     */
    public function index(): Response
    {
        $products = $this->productRepository->findAll();

        return $this->render('product/index.html.twig', ['products' => $products]);
    }

    /**
     * @Route("/product/add", name="product_new")
     */
    public function addAction(Request $request, FileUploader $fileUploader): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $saveFilename = $fileUploader->upload($imageFile);
                $product->setImage($saveFilename);
            }

            $this->productRepository->add($product);

            return $this->redirectToRoute('product_index');
        }

        return $this->renderForm('product/add.html.twig', ['form' => $form]);
    }

    /**
     * @Route("/product/{id}/update", name="product_edit")
     */
    public function updateAction($id, Request $request, FileUploader $fileUploader): Response
    {
        $product = $this->productRepository->find($id);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var UploadedFile $imageFile
             */
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = $fileUploader->upload($imageFile);
                $product->setImage($newFilename);
            }

            $this->productRepository->add($product);

            return $this->redirectToRoute('product_index');
        }

        return $this->renderForm('product/update.html.twig', [
            'form' => $form,
            'product' => $product
        ]);
    }

    /**
     * @Route("/product/{id}/delete", name="product_delete")
     */
    public function deleteAction($id)
    {
        $product = $this->productRepository->find($id);
        $this->productRepository->remove($product);

        return $this->redirectToRoute('product_index');
    }
}
