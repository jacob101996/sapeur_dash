<?php

namespace App\Controller;

use App\Form\AvisType;
use App\Repository\CategoryProductRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    private  $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em   = $em;
    }

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
            'products'  => $productRepository->findAll(),
            'collection_man'    => $productRepository->findRandomProdMan(),
            'collection_wife'   => $productRepository->findRandomProdWife(),
            'collection_baby'   => $productRepository->findRandomProdEnfant(),
            'collection_sport'  => $productRepository->findRandomProdSport(),
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
            'products'  => $tabProduct,
            'prod'      => $productRepository->findRandomProd(),
        ]);
    }

    /**
     * @Route("/product/view-detail-product/{{id}}", name="view_product_detail")
     * @param $id
     * @param CategoryProductRepository $categoryProductRepository
     * @param ProductRepository $productRepository
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function viewDetailProduct($id, CategoryProductRepository  $categoryProductRepository,
                                      ProductRepository $productRepository, Request $request){

        $prod = $productRepository->find($id);

        $form   = $this->createForm(AvisType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid())
        {
            $avi   = $form->getData();
            $avi->setDatecrea(new \DateTime('now'));
            $avi->setProduct($prod);

            $this->em->persist($avi);
            $this->em->flush();

            // Message et redirect

            $this->addFlash("success", "Votre avi est enregistré avec succes !");
            return $this->redirectToRoute('view_product_detail', ['id'  => $id]);
        }

        return $this->render('home/product_detail.html.twig', [
            'cats'      => $categoryProductRepository->findAll(),
            'product'   => $productRepository->find($id),
            'products'  => $prod->getPartner()->getProducts(),
            'partner'   => $prod->getPartner(),
            'prod'      => $productRepository->findRandomProd(),
            'form'      => $form->createView(),
            'avis'      => $prod->getAvis()
        ]);
    }
}

