<?php

namespace App\Controller;

use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Services\UploadFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private  $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em   = $em;
    }

    /**
     * @Route("/my/account/product/add-product/", name="product_add")
     * @param Request $request
     * @param UploadFile $file
     * @return Response
     * @throws \Exception
     */
    public function add(Request $request, UploadFile $file)
    {
        $form   = $this->createForm(ProductType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data   = $form->getData();

            $path = $_ENV["DIR_IMG_PRODUCT"];

            $image1     = $file->uploadFile($form['product_image']->getData(), $path);
            $image2     = $file->uploadFile($form['product_image1']->getData(), $path);
            $image3     = $file->uploadFile($form['product_image2']->getData(), $path);
            $image4     = $file->uploadFile($form['product_image3']->getData(), $path);

            if (!is_null($image1) && $image1 != ""){
                $data->setProductImage($image1);
            }
            if (!is_null($image2) && $image2 != ""){
                $data->setProductImage1($image2);
            }

            if (!is_null($image3) && $image3 != ""){
                $data->setProductImage2($image3);
            }

            if (!is_null($image4) && $image4 != ""){
                $data->setProductImage3($image4);
            }

            $data->setProductAt(new \DateTime("now"));

            $this->em->persist($data);
            $this->em->flush();

            $this->addFlash("success", "<i class='fas fa-check-circle'></i> Produit enregistré avec succès !");
            return $this->redirectToRoute("product_add");

        }
        return $this->render('product/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/my/account/product/list-product/", name="product_list")
     * @param ProductRepository $repository
     * @return Response
     */
    public function listProduct(ProductRepository $repository)
    {
        //dd($repository->findAll());
        return $this->render('product/list.html.twig', [
            'items' => $repository->findAll(),
        ]);
    }

    /**
     * @Route("/my/account/product/product/edit/{{id}}", name="product_edit")
     * @param $id
     * @param ProductRepository $productRepository
     * @param Request $request
     * @param UploadFile $file
     * @return Response
     */
    public function editProduct($id, ProductRepository $productRepository, Request $request, UploadFile $file){
        //dd($repository->findAll());

        $product    = $productRepository->find($id);
        $oldImage1  = $product->getProductImage();
        $oldImage2  = $product->getProductImage1();
        $oldImage3  = $product->getProductImage2();
        $oldImage4  = $product->getProductImage3();

        $form       = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();

            // Add medias
            $path = $_ENV["DIR_IMG_PRODUCT"];

            $image1 = $file->uploadFile($form['product_image']->getData(), $path);
            $image2 = $file->uploadFile($form['product_image1']->getData(), $path);
            $image3 = $file->uploadFile($form['product_image2']->getData(), $path);
            $image4 = $file->uploadFile($form['product_image3']->getData(), $path);


            if (!is_null($image1) && $image1 != "") {
                $data->setProductImage($image1);
            } else
                $data->setProductImage($oldImage1);

            if (!is_null($image2) && $image2 != "") {
                $data->setProductImage1($image2);
            } else
                $data->setProductImage1($oldImage2);

            if (!is_null($image3) && $image3 != "") {
                $data->setProductImage2($image3);
            } else
                $data->setProductImage2($oldImage3);

            if (!is_null($image4) && $image4 != "") {
                $data->setProductImage3($image4);
            }else
                $data->setProductImage3($oldImage4);

            $this->em->flush();

            // Message & redirection
            $this->addFlash("success", "Information(s) modifiée(s) avec succes !");
            return $this->redirectToRoute("product_list");

        }

        return $this->render('product/edit.html.twig', [
            'form'      => $form->createView(),
            'product'   => $product
        ]);
    }

    /**
     * @Route("/my/account/product/product/delete/{{id}}", name="product_delete")
     * @return Response
     */
    public function deleteProduct(){

    }

    /**
     * @Route("/add-card")
     * @param SessionInterface $session
     * @param Request $request
     * @return JsonResponse
     */
    public function addCart(SessionInterface $session, Request $request)
    {

        $id         = $request->get('idp');

        // Initialisation de la session
        $panier     = $session->get('session_cart', []);

        // Si le produit est déjà dans mon panier alors je rajoute
        /*if (!empty($panier[$id])){
            $panier[$id] ++;
        }else
            $panier[$id] = 1;*/

        $panier[$id] = 1;

        $session->set('session_cart', $panier);

        $tabQte = [];
        foreach ($session->get('session_cart', []) as $id => $quantity)
        {
            $tabQte []  = $quantity;
        }

        return new JsonResponse([
            'nbprod'    => array_sum($tabQte)
        ]);
    }

    /**
     * @Route("/add-favori")
     * @param SessionInterface $session
     * @param Request $request
     * @return JsonResponse
     */
    public function addFavori(SessionInterface $session, Request $request)
    {

        $id         = $request->get('idp');
        // Initialisation de la session
        $favori     = $session->get('session_heart', []);

        $favori[$id]    = 1;

        $session->set('session_heart', $favori);

        return new JsonResponse([
            'nbfavori'    => count($session->get('session_heart', []))
        ]);
    }

    /**
     * @Route("/modify-qte")
     * @param Request $request
     * @param SessionInterface $session
     * @param ProductRepository $repository
     * @return JsonResponse
     */
    public function modifyQte(Request $request, SessionInterface $session, ProductRepository $repository)
    {
        try {
            $idP        = $request->get('prod');
            $qte        = $request->get('qte');

            $panier     = $session->get('session_cart', []);

            $panier[$idP]  = $qte;

            $session->set('session_cart', $panier);



            $total      = null;
            $dix_pourcent      = null;
            $totalItems      = null;

            $product    = $repository->find($idP);

            if (is_null($product->getProductReduction())){
                $prix   = $product->getProductPrice();
            }else{
                $reductionPrice = ($product->getProductPrice() * $product->getProductReduction())/100;
                $prix   = (intval($product->getProductPrice()) + intval($reductionPrice));
            }

            $totalMontantProd = ($prix * $qte);


            $panierWithData     = [];


            foreach ($session->get('session_cart', []) as $id => $quantity)
            {
                $panierWithData []  = [
                    'product_session'       => $repository->find($id),
                    'qte'                   => $quantity
                ];
            }

            $total      = 0;

            foreach ($panierWithData as $item)
            {
                if (is_null($item['product_session']->getProductPrice())){
                    $prix   = $item['product_session']->getProductPrice();
                }else{
                    $reductionPrice = ($item['product_session']->getProductPrice() * $item['product_session']->getProductReduction())/100;
                    $prix           = (intval($item['product_session']->getProductPrice()) + intval($reductionPrice));
                }

                $totalItems = ($prix * $item['qte']);
                $total      += $totalItems;
                $dix_pourcent = ($total * 10)/100;
            }

            $tabQte = [];
            foreach ($session->get('session_cart',[]) as $id => $quantity) {
                $tabQte []  = $quantity;
            }

            return new JsonResponse([
                'total'     => $total,
                'montant'   => $totalMontantProd,
                'nbprod'    => array_sum($tabQte),
                'dix_pourcent'=> $dix_pourcent
            ]);

        }catch (\Exception $e) {
            die($e->getMessage());
        }
    }
}
