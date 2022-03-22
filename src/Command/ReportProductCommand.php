<?php

namespace App\Command;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ReportProductCommand extends Command
{
    protected static $defaultName = 'report:product';
    protected static $defaultDescription = 'Export information of product to CSV file.';

    /** @var ProductRepository */
    private $productRepository;
    /** @var CategoryRepository */
    private $categoryRepository;

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::OPTIONAL, 'Argument id category')
            ->addOption('nameFile', null, InputOption::VALUE_NONE, 'Name file csv')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = $input->getArgument('id');

        if ($id && $id != 1) {
            $category = $this->categoryRepository->find($id);
            $products = $category->getProducts();
        }else {
            $products = $this->productRepository->findAll();
        }
        $fileName = 'Report'. date('Ymd');
        if ($input->getOption('nameFile')) {
            $fileName =  $input->getOption('nameFile');
        }
        $outputBuffer = fopen($fileName. '.csv', 'w');
        fputcsv($outputBuffer,['Name', 'Category', 'ImageUrl', 'Description'], ',');
        foreach($products as $product) {
            fputcsv($outputBuffer, [
                $product->getName(),
                $product->getCategory(),
                $product->getImage(),
                $product->getDescription(),
            ], ',');
        }
        fclose($outputBuffer);

        return Command::SUCCESS;
    }

    /**
     * @return CategoryRepository
     */
    public function getCategoryRepository(): CategoryRepository
    {
        return $this->categoryRepository;
    }

    /**
     * @param CategoryRepository $categoryRepository
     * @return void
     */
    public function setCategoryRepository(CategoryRepository $categoryRepository): void
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return ProductRepository
     */
    public function getProductRepository(): ProductRepository
    {
        return $this->productRepository;
    }

    /**
     * @param ProductRepository $productRepository
     * @return void
     */
    public function setProductRepository(ProductRepository $productRepository): void
    {
        $this->productRepository = $productRepository;
    }
}
