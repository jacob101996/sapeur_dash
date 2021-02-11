<?php

namespace App\Controller;

use App\Form\AccountType;
use App\Form\EditAccountType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{

    private  $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em   = $em;
    }

    /**
     * @Route("/my/account/user-create", name="user_add")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     * @throws \Exception
     */
    public function create(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        // Initiamisation du formulaire
        $form   = $this->createForm(AccountType::class, null);
        $form->handleRequest($request);

        // Soumission du formulaire
        if ($form->isSubmitted() and $form->isValid()){
            $data   = $form->getData();

            $data->setUserAt(new \DateTime("now"));
            $data->setRoles([$request->get('account')['roles']]);
            // encodage mot de passe
            $plainText  =   $data->getPassword();

            $passwordEncod = $encoder->encodePassword($data, $plainText);

            $data->setPassword($passwordEncod);

            // Persiste et envoi en BD
            $this->em->persist($data);
            $this->em->flush();

            // Message flash et redirection
            $this->addFlash('success', "Compte utilisateur enregistré avec succes");
            return $this->redirectToRoute('user_add');

        }
        return $this->render('user/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/my/account/user-liste", name="user_list")
     * @param UserRepository $repository
     * @return Response
     */
    public function list(UserRepository $repository){
        return $this->render('user/list.html.twig', [
            'items' => $repository->findAll(),
        ]);
    }

    /**
     * @Route("/my/account/user-edit/{{id}}", name="user_edit")
     * @param $id
     * @param UserRepository $repository
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit($id, UserRepository $repository, Request $request)
    {
        $form       = $this->createForm(EditAccountType::class, $repository->find($id));
        $form->handleRequest($request);

        // soumission
        if ($form->isSubmitted() and $form->isValid()){
            $data   = $form->getData();
            $data->setRoles([$request->get('edit_account')['roles']]);

            $this->em->flush();

            // Message flash et redirection
            $this->addFlash("success", "Félicitation, information(s) modifiée(s) avec succes !");
            return $this->redirectToRoute('user_list');
        }
        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/my/account/user-delete/{{id}}", name="user_delete")
     * @param $id
     * @param UserRepository $repository
     * @return RedirectResponse
     */
    public function delete($id, UserRepository $repository){

        $this->em->remove($repository->find($id));
        $this->em->flush();

        // Message flash et redirection
        $this->addFlash('success', "Element supprimé avec success !");
        return $this->redirectToRoute('user_list');
    }
}
