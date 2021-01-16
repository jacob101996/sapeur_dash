<?php

namespace App\Controller;

use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Services\UploadFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private  $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em   = $em;
    }

    /**
     * @Route("/product/add-product/", name="product_add")
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

            if (!is_null($this->getUser()) && $this->getUser()->getRoles()[0] == "ROLE_PARTNER" )
            {
                $data->setProductRef($this->getUser()->getPartnerCode());
            }else
                $data->setProductRef("");

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
     * @Route("/product/list-product/", name="product_list")
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
     * @Route("/product/product/edit/{{id}}", name="product_edit")
     * @return Response
     */
    public function editProduct(){
        //dd($repository->findAll());
        return $this->render('product/edit.html.twig', [
            'items' => "",
        ]);
    }

    /**
     * @Route("/product/product/delete/{{id}}", name="product_delete")
     * @return Response
     */
    public function deleteProduct(){
    }
}
