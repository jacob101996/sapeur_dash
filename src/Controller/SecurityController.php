<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\User;
use App\Form\DemandePartenariatType;
use App\Form\UserType;
use App\Services\UploadFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private  $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em   = $em;
    }

    /**
     * @Route("/demande-de-partenariat", name="security_demande")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function demand(Request $request)
    {
        $form   = $this->createForm(DemandePartenariatType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data       = $form->getData();

            $data->setActivity($this->em->getRepository(Activity::class)->find($data->getActivity()));

            $data->setPartnerCode("SAP".random_int(1000,9999));
            $data->setStatusDemand("En attente");

            $this->em->persist($data);
            $this->em->flush();

            // Message flash
            $this->addFlash("success", "<i class='fas fa-check' style='color: white; margin-right: 10px'></i> Félicitation votre demande est enregistré avec succès et est en 
                cours de traitement nous vous contacterons pour la suite merci !");
            return $this->redirectToRoute("security_demande");
        }

        return $this->render('security/demande_partenariat.html.twig', [
            'form'      => $form->createView()
        ]);
    }

    /**
     * @Route("/connexion-espace", name="security_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        return $this->render('security/login.html.twig', [
            'error'     => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     * @param TokenStorageInterface $token
     * @return RedirectResponse
     */
    public function logout(TokenStorageInterface $token)
    {
        try {
            if ($token->getToken()->getUser())
            {
                $token->setToken(null);
            }
            return $this->redirectToRoute('home_index');
        }catch (\Exception $e)
        {
            die($e->getMessage());
        }

    }

    /**
     * @return int
     */
    public function generateUniqueCode()
    {
        $code   = mt_rand(1111, 9999);

        foreach ($this->em->getRepository(User::class)->findAll() as $item)
        {
            if ($item->getPartnerCode() == $code)
            {
                $code   = strval(mt_rand(010101, 99999));
            }
        }

        return $code;
    }
}
