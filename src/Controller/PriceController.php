<?php

namespace App\Controller;

use App\Entity\Price;
use App\Entity\Product;
use App\Form\InsertPriceType;
use App\Form\UpdatePriceType;
use App\Repository\PriceRepository;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PriceController extends AbstractController
{
    private $priceRepository;

    public function __construct(PriceRepository $priceRepository)
    {
        $this->priceRepository = $priceRepository;
    }
    /**
     * @Route("/product/{id}/prices", name="price_index")
     */
    public function index(Request $request, Product $product, PaginatorInterface $paginator): Response
    {
        $prices = $this->priceRepository->findBy(['product' => $product], ['product' => 'asc']);

        $prices = $paginator->paginate(
            $prices,
            $request->query->getInt('page', 1),
            4
        );

        return $this->render('price/index.html.twig', [
            'prices' => $prices,
            'product' => $product,
        ]);
    }

    /**
     * @Route("/product/{id}/price/new", name="price_new")
     */
    public function new(Request $request, Product $product): Response
    {
        $price = new Price();

        $form = $this->createForm(InsertPriceType::class, $price);
        $form ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $size = $price->getSize();

            $checkPriceExist = $this->priceRepository->findBy(['product' => $product, 'size' => $size]);

            if ($checkPriceExist) {
                $priceId = $checkPriceExist[0]->getId();
                return $this->redirectToRoute('price_edit', ['id' => $product->getId(), 'idPrice' => $priceId]);
            }

            $price->setProduct($product);
            $this->priceRepository->add($price);

            $this->addFlash(
                'success',
                'Add price successfully!'
            );

            return $this->redirectToRoute('price_index', ['id' => $product->getId()]);
        }

        return $this->renderForm('price/new.html.twig', [
            'form' => $form,
            'product' => $product,
        ]);
    }

    /**
     * @Route("/product/{id}/price/{idPrice}/delete", name="price_delete")
     */
    public function delete($id, $idPrice): Response
    {
        $price = $this->priceRepository->find($idPrice);
        $this->priceRepository->remove($price);

        return $this->redirectToRoute('price_index', ['id' => $id], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("product/{id}/price/{idPrice}", name="price_edit")
     */
    public function editPrice(Request $request, Product $product, $idPrice, ProductRepository $productRepository): Response
    {
        $price = $this->priceRepository->find($idPrice);
        $size = $price->getSize();

        $form = $this->createForm(UpdatePriceType::class, $price);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->priceRepository->add($price);

            $this->addFlash(
                'success',
                'Update price successfully!'
            );

            return $this->redirectToRoute('price_index', ['id' => $product->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('price/edit.html.twig', [
            'product' => $product,
            'size' => $size,
            'form' => $form,
        ]);
    }
}
