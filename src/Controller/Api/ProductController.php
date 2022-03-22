<?php

namespace App\Controller\Api;

use _PHPStan_ae8980142\Nette\Schema\ValidationException;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\FileUploader;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

        $serializer = SerializerBuilder::create()->build();
        $convertToJson = $serializer->serialize($products, 'json', SerializationContext::create()->setGroups(array('show')));
        $products = $serializer->deserialize($convertToJson, 'array', 'json');

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
        $serializer = SerializerBuilder::create()->build();
        $convertToJson = $serializer->serialize($product, 'json', SerializationContext::create()->setGroups(array('show')));
        $product = $serializer->deserialize($convertToJson, 'array', 'json');

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
        $requestData = json_decode($request->getContent(), true);
        $form->submit($requestData);
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

    /**
     * @Rest\Put("/products/{id}")
     * @param Request $request
     * @param integer $id
     * @return Response
     */
    public function updateProduct(int $id, Request $request): Response
    {
        $product = $this->productRepository->find($id);
        if(!$product) {
            $view = $this->view(['error' => 'Product is not found.'], Response::HTTP_NOT_FOUND);
            return $this->handleView($view);
        }

        $form = $this->createForm(ProductType::class, $product);
        $requestData = json_decode($request->getContent(), true);
        $form->submit($requestData);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setImage($requestData['image']);
            $this->productRepository->add($product);

            return $this->handleView($this->view($product, Response::HTTP_NO_CONTENT));
        }

        return $this->handleView($this->view($form->getErrors()));
    }

    /**
     * @Rest\Post("/products/image")
     * @param Request $request
     * @param FileUploader $fileUploader
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function updateImageProduct(Request $request, FileUploader $fileUploader, ValidatorInterface $validator): Response
    {
        $uploadFile = $request->files->get('image');

        if (!$uploadFile) {
            return $this->handleView($this->view(['error' => 'Please choose image to upload.'], Response::HTTP_BAD_REQUEST));
        }

        $errors = $validator->validate($uploadFile, new Image([
            'maxSize' => '5M',
            'mimeTypes' => [
                "image/jpeg",
                "image/jpg",
                "image/png",
            ],
            'maxSizeMessage' => 'File is too large.',
            'mimeTypesMessage' => 'Please upload a valid Image file.',
        ]));

        if (count($errors)) {
            return $this->handleView($this->view(['error' => $errors], Response::HTTP_BAD_REQUEST));
        }
        $saveFile = $fileUploader->upload($uploadFile);

        return $this->handleView($this->view(['imageUrl' => $saveFile], Response::HTTP_CREATED));
    }

    /**
     * @Rest\Delete("/products/{id}")
     * @param integer $id
     * @return Response
     */
    public function deleteProduct(int $id): Response
    {
        try {
            $product = $this->productRepository->find($id);
            if (!$product) {
                return $this->handleView($this->view(
                    ['error' => 'No product was found with this id.'],
                    Response::HTTP_NOT_FOUND
                ));
            }

            $this->productRepository->remove($product);

            return $this->handleView($this->view([], Response::HTTP_NO_CONTENT));
        } catch (\Exception $e) {
            //Need to add log the error message
        }

        return $this->handleView($this->view(
            ['error' => 'Something went wrong! Please contact support.'],
            Response::HTTP_INTERNAL_SERVER_ERROR
        ));
    }
}
