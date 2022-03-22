<?php

namespace App\Controller;

use App\Form\ReportProductType;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

class ReportProductController extends AbstractController
{
    /**
     * @Route("/report_product", name="report_product")
     */
    public function index(Request $request, KernelInterface $kernel): Response
    {
        $form = $this->createForm(ReportProductType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category = $form->get('category')->getData();

            $application = new Application($kernel);
            $application->setAutoExit(false);

            $categoryId = $category->getId();

            $input = new ArrayInput([
                'command' => 'report:product',
                'id' => $categoryId,
            ]);

            $output = new BufferedOutput();
            $application->run($input, $output);

            $content = $output->fetch();
            $this->addFlash('success', $content);
        }

        return $this->renderForm('report_product/index.html.twig', [
            'form' => $form,
        ]);
    }
}
