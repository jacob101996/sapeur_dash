<?php

namespace App\Controller;

use App\Form\UserType;
use App\Services\UploadFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PartnerController extends AbstractController
{
    /**
     * @Route("/partner/add-partner", name="partner_add")
     * @param Request $request
     * @param UploadFile $file
     * @return Response
     */
    public function add(Request $request, UploadFile $file)
    {
        $form   = $this->createForm(UserType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data   = $form->getData();

            $path = $_ENV["DIR_IMG_PRODUCT"];

            $image1     = $file->uploadFile($form['partner_room_1']->getData(), $path);
            $image2     = $file->uploadFile($form['partner_room_2']->getData(), $path);

            if (!is_null($image1) && $image1 != ""){
                $data->setProductImage($image1);
            }
            if (!is_null($image2) && $image2 != ""){
                $data->setProductImage1($image2);
            }

            dd($data);
        }
        return $this->render('partner/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
