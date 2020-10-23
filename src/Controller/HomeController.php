<?php

namespace App\Controller;

use App\Repository\CategoryProductRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home_index")
     * @param CategoryProductRepository $categoryProductRepository
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(CategoryProductRepository $categoryProductRepository, ProductRepository $productRepository)
    {
        return $this->render('home/index.html.twig', [
            'cats'      => $categoryProductRepository->findAll(),
            'products'  => $productRepository->findAll()
        ]);
    }

    /**
     * @Route("/category/{slug}/", name="view_product_by_category")
     * @param CategoryProductRepository $categoryProductRepository
     * @param ProductRepository $productRepository
     * @param Request $request
     * @return Response
     */
    public function viewProductByCategory(
        CategoryProductRepository $categoryProductRepository,
        ProductRepository $productRepository, Request $request){

        $tabProduct     = [];

        foreach ($productRepository->findAll() as $product)
        {
            if (strtolower($product->getCategory()->getLibelleFr()) == strtolower($request->get('slug'))){

                $tabProduct []  = $product;

            }
        }

        return $this->render('home/view_product_by_category.html.twig', [
            'cats'      => $categoryProductRepository->findAll(),
            'products'  => $tabProduct
        ]);
    }
}

