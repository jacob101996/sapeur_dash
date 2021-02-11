<?php

namespace App\Controller;

use App\Form\PartnerType;
use App\Repository\PartnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PartnerController extends AbstractController
{

    private  $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em   = $em;
    }

    /**
     * @Route("/my/account/partner/add-partner", name="partner_add")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function add(Request $request)
    {
        $form   = $this->createForm(PartnerType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data   = $form->getData();

            // Date time
            $data->setDatecrea(new \DateTime("now"));

            $data->setPartnerCode($request->get('code_partner'));

            // Persistence et flush
            $this->em->persist($data);

            $this->em->flush();

            // Message flash et redirect

            $this->addFlash("success", "<i class='fas fa-check mr-2'></i>Partenaire enregistré avec succes !");
            return $this->redirectToRoute("partner_add");

        }
        return $this->render('partner/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/generate-unique-partner-code")
     * @param Request $request
     * @param PartnerRepository $partnerRepository
     * @return JsonResponse
     */
    public function generatePartnerCode(Request $request, PartnerRepository $partnerRepository)
    {
        // Recuperation des donnees
        $name           = $request->get('name');
        $last_name      = $request->get('last_name');

        // Generation du code
        $code   = "SAP".mt_rand(1000, 9999);

        // Verification du code en base de donnee
        if (count($partnerRepository->findByCode($code)) > 0) {
            $code   = "SAP".mt_rand(1111, 9999);
        }

        return new JsonResponse([
            'code'  => $code
        ]);
    }


    /**
     * @Route("/my/account/partner/list-partner", name="partner_list")
     * @param PartnerRepository $partnerRepository
     * @return Response
     */
    public function listPartner(PartnerRepository $partnerRepository)
    {
        return $this->render('partner/list.html.twig', [
            'partners' => $partnerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/my/account/partner/edit-partner/{{id}}", name="partner_edit")
     * @param $id
     * @param PartnerRepository $partnerRepository
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editPartner($id, PartnerRepository $partnerRepository, Request$request)
    {
        $partner    = $partnerRepository->find($id);
        $form       = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            // Message flash & redirection
            $this->addFlash("success", "Information(s) modifiée(s) avec succes !");
            return $this->redirectToRoute("partner_list");
        }
        return $this->render('partner/edit.html.twig', [
            'form' => $form->createView(),
            'partner_code'  => $partner->getPartnerCode()
        ]);
    }

    /**
     * @Route("/my/account/partner/delete-partner/{{id}}", name="partner_delete")
     * @param $id
     * @param PartnerRepository $partnerRepository
     * @return RedirectResponse
     */
    public function deletePartner($id, PartnerRepository $partnerRepository)
    {
        $partner    = $partnerRepository->find($id);

        // Suppression des element lié
        foreach ($partner->getProducts() as $product)
        {
            $this->em->remove($product);
            $this->em->flush();
        }

        $this->em->remove($partner);
        $this->em->flush();

        // Message flash & redirection
        $this->addFlash("success", "Partner supprimer avec succes !");
        return $this->redirectToRoute("partner_list");

    }
}
