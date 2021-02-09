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
