<?php

namespace App\Controller;

use App\Form\AvisType;
use App\Repository\CategoryProductRepository;
use App\Repository\PartnerRepository;
use App\Repository\ProductRepository;
use App\Repository\SubCategoryProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
     * @param SessionInterface $session
     * @return Response
     */
    public function index(CategoryProductRepository $categoryProductRepository,
                          ProductRepository $productRepository, SessionInterface $session)
    {
        $tabQte = [];
        foreach ($session->get('session_cart', []) as $id => $quantity)
        {
            $tabQte []  = $quantity;
        }

        return $this->render('home/index.html.twig', [
            'cats'      => $categoryProductRepository->findAll(),
            'products'  => $productRepository->findAll(),
            'collection_man'    => $productRepository->findRandomProdMan(),
            'collection_wife'   => $productRepository->findRandomProdWife(),
            'collection_baby'   => $productRepository->findRandomProdEnfant(),
            'collection_sport'  => $productRepository->findRandomProdSport(),
            'nbprod'    => array_sum($tabQte),
            'nbfavori'                => count($session->get('session_heart', [])),
        ]);
    }

    /**
     * @Route("product/view/{slug}/", name="view_product_by_category")
     * @param CategoryProductRepository $categoryProductRepository
     * @param ProductRepository $productRepository
     * @param $slug
     * @param SessionInterface $session
     * @return Response
     */
    public function viewProductByCategory(
        CategoryProductRepository $categoryProductRepository,
        ProductRepository $productRepository, $slug, SessionInterface $session){

        $tabQte = [];

        foreach ($session->get('session_cart', []) as $id => $quantity)
        {
            $tabQte []  = $quantity;
        }

        //dd($categoryProductRepository->findByCat($slug));

        return $this->render('home/view_product_by_category.html.twig', [
            'cat'       => $categoryProductRepository->findByCat($slug),
            'products'  => $productRepository->findProductByCat($categoryProductRepository->findByCat($slug)),
            'prod'      => $productRepository->findRandomProd(),
            'nbprod'    => array_sum($tabQte),
            'nbfavori'  => count($session->get('session_heart', [])),
            'cats'      => $categoryProductRepository->findAll()
        ]);
    }

    /**
     * @Route("/product/view-detail-product/{{id}}", name="view_product_detail")
     * @param CategoryProductRepository $categoryProductRepository
     * @param ProductRepository $productRepository
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     * @throws \Exception
     */
    public function viewDetailProductByCat(CategoryProductRepository  $categoryProductRepository,
                                      ProductRepository $productRepository, Request $request, SessionInterface $session){

        $tabQte = [];
        foreach ($session->get('session_cart', []) as $id => $quantity)
        {
            $tabQte []  = $quantity;
        }

        $prod = $productRepository->find($request->get('id'));

        $form   = $this->createForm(AvisType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $avi   = $form->getData();
            $avi->setDatecrea(new \DateTime('now'));
            $avi->setProduct($prod);

            $this->em->persist($avi);
            $this->em->flush();

            // Message et redirect
            $this->addFlash("success", "Votre avi est enregistré avec succes !");
            return $this->redirectToRoute('view_product_detail', ['id'  => $request->get('id')]);
        }

        return $this->render('home/product_detail.html.twig', [
            'cats'           => $categoryProductRepository->findAll(),
            'product_item'   => $prod,
            'products'       => $prod->getPartner()->getProducts(),
            'partner'        => $prod->getPartner(),
            'prod'      => $productRepository->findRandomProd(),
            'form'      => $form->createView(),
            'avis'      => $prod->getAvis(),
            'nbprod'    => array_sum($tabQte),
            'nbfavori'                => count($session->get('session_heart', [])),
        ]);
    }

    /**
     * @Route("/view/{cat}/{sub}/{quality}", name="view_product_by_cat_and_sub_quality")
     * @param $cat
     * @param $sub
     * @param $quality
     * @param CategoryProductRepository $categoryProductRepository
     * @param ProductRepository $productRepository
     * @param SessionInterface $session
     * @param SubCategoryProductRepository $subCategoryProductRepository
     * @return RedirectResponse|Response
     */
    public function viewDetailProductBySubCatQuality($cat,$sub,CategoryProductRepository  $categoryProductRepository,
                                              ProductRepository $productRepository, SessionInterface $session,
                                              SubCategoryProductRepository $subCategoryProductRepository, $quality)
    {

        $tabQte = [];

        foreach ($session->get('session_cart', []) as $id => $quantity)
        {
            $tabQte []  = $quantity;
        }

        $subCat = $subCategoryProductRepository->findOneBySubCat($categoryProductRepository->findByCat($cat),$sub);

        return $this->render('home/view_product_by_category.html.twig', [
            'cat'      => $categoryProductRepository->findByCat($cat),
            'products'  => $productRepository->findProductByCatAndSubAndQuality($categoryProductRepository->findByCat($cat),
                $subCat, $quality),
            'prod'      => $productRepository->findRandomProd(),
            'nbprod'    => array_sum($tabQte),
            'nbfavori'                => count($session->get('session_heart', [])),
            'cats'      => $categoryProductRepository->findAll()
        ]);
    }

    /**
     * @Route("/view/{cat}/{sub}", name="view_product_by_cat_and_sub")
     * @param $cat
     * @param $sub
     * @param \App\Repository\CategoryProductRepository $categoryProductRepository
     * @param \App\Repository\ProductRepository $productRepository
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param \App\Repository\SubCategoryProductRepository $subCategoryProductRepository
     * @return mixed
     */
    public function viewDetailProductBySubCat($cat,$sub,CategoryProductRepository  $categoryProductRepository,
                                              ProductRepository $productRepository, SessionInterface $session,
                                              SubCategoryProductRepository $subCategoryProductRepository)
    {

        $tabQte = [];

        foreach ($session->get('session_cart', []) as $id => $quantity)
        {
            $tabQte []  = $quantity;
        }

        $subCat = $subCategoryProductRepository->findOneBySubCat($categoryProductRepository->findByCat($cat),$sub);

        return $this->render('home/view_product_by_category.html.twig', [
            'cat'      => $categoryProductRepository->findByCat($cat),
            'products'  => $productRepository->findProductByCatAndSub($categoryProductRepository->findByCat($cat),
                $subCat),
            'prod'      => $productRepository->findRandomProd(),
            'nbprod'    => array_sum($tabQte),
            'nbfavori'                => count($session->get('session_heart', [])),
            'cats'      => $categoryProductRepository->findAll()
        ]);
    }


    /**
     * @Route("/mon-panier", name="my_cart")
     * @param ProductRepository $productRepository
     * @param SessionInterface $session
     * @param CategoryProductRepository $categoryProductRepository
     * @return Response
     */
    public function myCart(ProductRepository $productRepository, SessionInterface $session,
                           CategoryProductRepository $categoryProductRepository)
    {
        $panier             = $session->get('session_cart', []);
        $total      = null;
        $prix       = null;
        $reductionPrice  = null;
        $panierWithData     = [];
        $totalItems         = null;
        $dixPourcent        = null;


        foreach ($panier as $id => $quantity)
        {
            $panierWithData []  = [
                'product_session'       => $productRepository->find($id),
                'qte'                   => $quantity
            ];
        }

        // Parcourir le tableau calcul price
        foreach ($panierWithData as $item) {
            if (is_null($item['product_session']->getProductReduction())){
                $prix   = $item['product_session']->getProductPrice();
            }else{
                $reductionPrice = ($item['product_session']->getProductPrice() * $item['product_session']->getProductReduction())/100;
                $prix   = (intval($item['product_session']->getProductPrice()) + intval($reductionPrice));
            }

            $totalItems = ($prix * $item['qte']);
            $total      += $totalItems;
        }

        if ($total <= 4999){
            $dixPourcent = $total;
        }else
            $dixPourcent = ($total * 10)/100;

        // Count nbr item in panier
        $tabQte = [];
        foreach ($panier as $id => $quantity)
        {
            $tabQte []  = $quantity;
        }

        return $this->render('home/my_cart.html.twig', [
            'products'                => $productRepository->findManiProductItems(),
            'nbprod'                  => array_sum($tabQte),
            'cats'                    => $categoryProductRepository->findAll(),
            'product_session'         => $panierWithData,
            'total'                   => $total,
            'nbfavori'                => count($session->get('session_heart', [])),
            'dix_pourcent'            => $dixPourcent
        ]);
    }

    /**
     * @Route("/mon-panier/remove-product/{{id}}", name="remove_prod")
     * @param SessionInterface $session
     * @param $id
     * @return RedirectResponse
     */
    public function removeProdByCart(SessionInterface $session, $id){

        $panier = $session->get('session_cart', []);


        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }
        $session->set('session_cart', $panier);

        $this->addFlash("success", "Produit supprimé avec succes !");
        return $this->redirectToRoute("my_cart");
    }


    /**
     * @Route("/product/mon-panier/remove-all-product", name="remove_all_prod")
     * @param SessionInterface $session
     * @return RedirectResponse
     */
    public function removeAllProdByCart(SessionInterface $session){

        $session->remove('session_cart');

        $this->addFlash("success", "Votre panier a été vider avec succes !");
        return $this->redirectToRoute("my_cart");
    }

    /**
     * @Route("/product/all/product-of-seller/{{slug}}", name="all_prod_of_seller")
     * @param $slug
     * @param PartnerRepository $partnerRepository
     * @param SessionInterface $session
     * @param CategoryProductRepository $categoryProductRepository
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function allProductForPartner($slug, PartnerRepository $partnerRepository,
                                         SessionInterface $session,
                                         CategoryProductRepository $categoryProductRepository,
                                         ProductRepository $productRepository){

        $partner    = $partnerRepository->findOneByPartnerCode($slug);

        $panier             = $session->get('session_cart', []);

        // Count nbr item in panier
        $tabQte = [];
        foreach ($panier as $id => $quantity)
        {
            $tabQte []  = $quantity;
        }

        return $this->render('home/all_product_of_seller.html.twig', [
            'products'                => $partner->getProducts(),
            'nbprod'                  => array_sum($tabQte),
            'cats'                    => $categoryProductRepository->findAll(),
            'nbfavori'                => count($session->get('session_heart', [])),
            'seller'                  => $partner->getPartnerShopName(),
            'prod'                    => $productRepository->findRandomProd(),
        ]);
    }


    /**
     * @Route("/mes-favoris", name="my_wishlist")
     * @param ProductRepository $productRepository
     * @param CategoryProductRepository $categoryProductRepository
     * @param SessionInterface $session
     * @return Response
     */
    public function myWishlist(ProductRepository $productRepository,
                               CategoryProductRepository $categoryProductRepository, SessionInterface $session)
    {
        $panier             = $session->get('session_cart', []);

        // Count nbr item in panier
        $tabQte = [];
        foreach ($panier as $id => $quantity)
        {
            $tabQte []  = $quantity;
        }

        $favori             = $session->get('session_heart', []);

        $favoriWithData     = [];


        foreach ($favori as $id => $quantity)
        {
            $favoriWithData []  = [
                'product'       => $productRepository->find($id),
                'qte'           => $quantity
            ];
        }

        return $this->render('home/my_wishlist.html.twig', [
            'nbprod'                  => array_sum($tabQte),
            'cats'                    => $categoryProductRepository->findAll(),
            'nbfavori'                => count($session->get('session_heart', [])),
            'prod'                    => $productRepository->findRandomProd(),
            'products'                => $favoriWithData
        ]);
    }

    /**
     * @Route("/mes-favoris/remove-product/{{id}}", name="remove_prod_in_whishlist")
     * @param SessionInterface $session
     * @param $id
     * @return RedirectResponse
     */
    public function removeProdByWishlist(SessionInterface $session, $id){

        $favori = $session->get('session_heart', []);


        if (!empty($favori[$id])) {
            unset($favori[$id]);
        }
        $session->set('session_heart', $favori);

        $this->addFlash("success", "Produit supprimé avec succes !");
        return $this->redirectToRoute("my_wishlist");
    }

    /**
     * @Route("/search/product", name="search_product")
     * @param \App\Repository\ProductRepository $productRepository
     * @param \App\Repository\CategoryProductRepository $categoryProductRepository
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchProduct(ProductRepository $productRepository,
                                  CategoryProductRepository $categoryProductRepository,
                                  SessionInterface $session, Request $request){

        $panier             = $session->get('session_cart', []);

        // Count nbr item in panier
        $tabQte = [];
        foreach ($panier as $id => $quantity)
        {
            $tabQte []  = $quantity;
        }

        $favori             = $session->get('session_heart', []);

        $favoriWithData     = [];


        foreach ($favori as $id => $quantity)
        {
            $favoriWithData []  = [
                'product'       => $productRepository->find($id),
                'qte'           => $quantity
            ];
        }

        return $this->render('home/search_product.html.twig', [
            'nbprod'                  => array_sum($tabQte),
            'cats'                    => $categoryProductRepository->findAll(),
            'nbfavori'                => count($session->get('session_heart', [])),
            'prod'                    => $productRepository->findRandomProd(),
            'products'                => $productRepository->findByProductSearch($request->get('search'))
        ]);
    }

    /**
     * @Route("/search/product/by/{quality}", name="search_product_by_quality")
     * @param ProductRepository $productRepository
     * @param CategoryProductRepository $categoryProductRepository
     * @param SessionInterface $session
     * @param $quality
     * @return mixed
     */
    public function searchProductByQuality(ProductRepository $productRepository,
                                  CategoryProductRepository $categoryProductRepository,
                                  SessionInterface $session, $quality){

        $panier             = $session->get('session_cart', []);

        // Count nbr item in panier
        $tabQte = [];
        foreach ($panier as $id => $quantity)
        {
            $tabQte []  = $quantity;
        }

        $favori             = $session->get('session_heart', []);

        $favoriWithData     = [];


        foreach ($favori as $id => $quantity)
        {
            $favoriWithData []  = [
                'product'       => $productRepository->find($id),
                'qte'           => $quantity
            ];
        }

        return $this->render('home/search_product.html.twig', [
            'nbprod'                  => array_sum($tabQte),
            'cats'                    => $categoryProductRepository->findAll(),
            'nbfavori'                => count($session->get('session_heart', [])),
            'prod'                    => $productRepository->findRandomProd(),
            'products'                => $productRepository->findProductByQuality($quality)
        ]);
    }

}

