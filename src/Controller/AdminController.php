<?php

namespace App\Controller;

use App\Entity\Command;
use App\Repository\CommandRepository;
use App\Repository\PartnerRepository;
use App\Repository\ProductRepository;
use App\Repository\SavRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    private  $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em   = $em;
    }

    /**
     * @Route("/my/account/", name="admin_index")
     * @param ProductRepository $productRepository
     * @param PartnerRepository $partnerRepository
     * @param CommandRepository $commandRepository
     * @param SavRepository $savRepository
     * @return Response
     */
    public function index(ProductRepository $productRepository, PartnerRepository $partnerRepository,
                          CommandRepository $commandRepository, SavRepository $savRepository)
    {
        return $this->render('admin/index.html.twig', [
            'products'  => $productRepository->findAll(),
            'partners'  => $partnerRepository->findAll(),
            'commands'  => $commandRepository->findAll(),
            'sav'       => $savRepository->findAll(),
        ]);
    }

    /**
     * @Route("my/account/statistic/chart")
     * @return JsonResponse
     */
    public function chartAjax()
    {
        $tabRes = [];
        $resFinal = [];
        try {
            $commands    = $this->em->getRepository(Command::class)
                ->dataCommand();

            foreach ($commands as $item){
                $tab = explode('-', $item['date_data']);
                $tabRes[$tab[1]] = $item['1'];
            }
            $tabIndex = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
            $keyTabRes = array_keys($tabRes);

            foreach ($tabIndex as $key) {
                if (!in_array($key, $keyTabRes)) {
                    $resFinal[$key] = 0;
                }
                else {
                    $resFinal[$key] = $tabRes[$key];
                }
            }

            $nb = 0;
            $resultat = [];
            foreach ($resFinal as $key => $value) {
                $resultat[$nb] = intval($value);
                $nb++;
            }

            return new JsonResponse([
                $resultat
            ]);
        }
        catch (\Exception $e){
            die($e->getMessage());
        }

    }


    /**
     * @Route("/my/account/commande-du-jour", name="admin_cmd_to_day")
     * @param CommandRepository $commandRepository
     * @return Response
     */
    public function returnCmdOfDay(CommandRepository $commandRepository)
    {
        $toDay  = date("d-m-Y");

        $tabCmdToDay    = [];

        foreach ( $commandRepository->findAll() as $item){

            if ($item->getDateDelivery()->format('d-m-Y') == $toDay)
            {
                $tabCmdToDay    []  = $item;
            }
        }
        return $this->render('admin/command_list.html.twig', [
            'commands'  => $tabCmdToDay,
        ]);
    }

    /**
     * @Route("/my/account/commande-all", name="admin_cmd_all")
     * @param CommandRepository $commandRepository
     * @return Response
     */
    public function returnCmdAll(CommandRepository $commandRepository)
    {
        return $this->render('admin/command_list.html.twig', [
            'commands'  => $commandRepository->findAll(),
        ]);
    }
}
