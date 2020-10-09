<?php

namespace App\Controller;

use App\Entity\User;
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
     * @Route("/creation-de-compte", name="security_register")
     * @param Request $request
     * @param UploadFile $file
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     * @throws \Exception
     */
    public function register(Request $request, UploadFile $file, UserPasswordEncoderInterface $encoder)
    {
        $form   = $this->createForm(UserType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data       = $form->getData();

            $data->setUserAt(new \DateTime('now'));

            $passwordEncode  = $encoder->encodePassword($data, $data->getPassword());

            $data->setPassword($passwordEncode);
            $data->setRoles(["ROLE_PARTNER"]);
            $data->setPartnerCode($this->generateUniqueCode());
            $data->setUsername(strtolower(substr($data->getPartnerLastname(), 0,1)."_".$data->getPartnerFirstname()));

            $this->em->persist($data);
            $this->em->flush();

            // Message flash
            $this->addFlash("success", "<i class='fas fa-check' style='color: white'></i> Félicitation votre compte est enregistré avec succès, merci pour votre fidélité !");
            return $this->redirectToRoute("security_login");
        }

        return $this->render('security/register.html.twig', [
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
