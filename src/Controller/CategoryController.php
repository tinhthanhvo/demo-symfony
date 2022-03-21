<?php

namespace App\Controller;

use App\Form\CategoryType;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/category", name="category_index")
     */
    public function index(): Response
    {
        $categories = $this->categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/category/create", name="category_create")
     */
    public function create(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->categoryRepository->add($category);
                $this->addFlash(
                    'success',
                    'Created category successfully!'
                );
            } catch (\Exception $e) {
                $this->addFlash(
                    'error',
                    'Created category failed!'
                );
            }

            return $this->redirectToRoute('category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/create.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/category/{id}/edit", name="category_edit")
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryRepository->add($category);

            $this->addFlash(
                'success',
                'Updated category successfully!'
            );

            return $this->redirectToRoute('category_edit', ['id' => $category->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/category/delete/{id}", name="category_delete", methods={"POST"})
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($category->getId() != 1 && $this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $this->categoryRepository->remove($category);
        }

        $this->addFlash(
            'success',
            'Deleted category successfully!'
        );

        return $this->redirectToRoute('category_index', [], Response::HTTP_SEE_OTHER);
    }
}
