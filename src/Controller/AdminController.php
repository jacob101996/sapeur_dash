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
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        // Commande du jour
        $toDay  = date("d-m-Y");

        $tabCmdToDay    = [];

        foreach ($commandRepository->findAllCommand() as $item){

            if ($item->getCommandAt()->format('d-m-Y') == $toDay)
            {
                $tabCmdToDay    []  = $item;
            }
        }

        return $this->render('admin/index.html.twig', [
            'products'  => $productRepository->findAll(),
            'partners'  => $partnerRepository->findAll(),
            'commands'  => $commandRepository->findAll(),
            'sav'       => $savRepository->findAll(),
            'commandToDay'=> $tabCmdToDay,
            'moov_money' => count($commandRepository->findByBuyedBy('FlOOZ')),
            'orange_money' => count($commandRepository->findByBuyedBy('OMCIV2')),
            'mtn_money' => count($commandRepository->findByBuyedBy('MOMO')),
            'visa_card' => count($commandRepository->findByBuyedBy('CARD')),
        ]);
    }

    /**
     * @Route("/my/account/command/detail/{code_facture}", name="admin_detail_command")
     * @param $code_facture
     * @param CommandRepository $commandRepository
     * @return Response
     */
    public function detailCommand($code_facture,
                                  CommandRepository $commandRepository){

        // Commande du jour
        $toDay  = date("d-m-Y");

        $tabCmdToDay    = [];

        foreach ($commandRepository->findAllCommand() as $item){

            if ($item->getCommandAt()->format('d-m-Y') == $toDay)
            {
                $tabCmdToDay    []  = $item;
            }
        }

        return $this->render('admin/detail_command.html.twig', [
            'commandToDay'  => $tabCmdToDay,
            'command'       => $commandRepository->findOneByNumberFacture($code_facture)
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

        foreach ( $commandRepository->findAllCommand() as $item){

            if ($item->getCommandAt()->format('d-m-Y') == $toDay)
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
            'commands'  => $commandRepository->findAllCommand(),
        ]);
    }

    /**
     * @Route("/my/account/send-sms-client/success/{ref}", name="send_sms_clt")
     * @param $ref
     * @param CommandRepository $commandRepository
     */
    public function sendSmsClientSuccess($ref, CommandRepository $commandRepository){

        $command = $commandRepository->findByRefCmd($ref);

       // dd($command[0]);

        $msg = "Cher(e) client(e), ".$command[0]->getNameClt()." , votre commande a ete validee avec succes, un conseiller client vous contactera pour la livraison. \nPour plus d'info (+225) 01 50 50 50 23";
        //dd($command[0]->getTelClt());
        $this->sendSms($msg, $command[0]->getTelClt());

        $command[0]->setEtat('success');
        $command[0]->setIsBuyed(true);
        $command[0]->setSendSms(true);

        $this->em->flush();

        $this->addFlash('success', 'SMS trasmis au client');

        return $this->redirectToRoute('admin_detail_command', [
            'code_facture'  => $command[0]->getNumberFacture()
        ]);
    }

    /**
     * @Route("/my/account/send-sms-client/echec/{ref}", name="send_sms_clt_echec")
     * @param $ref
     * @param CommandRepository $commandRepository
     * @return RedirectResponse
     */
    public function sendSmsClientEchec($ref, CommandRepository $commandRepository){

        $command = $commandRepository->findByRefCmd($ref);

        // dd($command[0]);

        $msg = "Cher(e) client(e), ".$command[0]->getNameClt()." , votre commande a échoue. \nPour plus de détails, veuillez contacter notre service client au (+225) 01 50 50 50 23";
        //dd($command[0]->getTelClt());
        $this->sendSms($msg, $command[0]->getTelClt());

        $command[0]->setEtat('echec');
        $command[0]->setIsBuyed(false);
        $command[0]->setSendSms(true);

        $this->em->flush();

        $this->addFlash('success', 'SMS trasmis au client');

        return $this->redirectToRoute('admin_detail_command', [
            'code_facture'  => $command[0]->getNumberFacture()
        ]);
    }

    public function sendSms($message, $phone){

        $url = "http://monalertesms.com/api";

        $args = [
            'userid'    => $_ENV['USER_ID'],
            'password'  => $_ENV['USER_PWD'],
            'message'   => urlencode($message),
            'phone'     => $phone,
            'sender'    => urlencode("SAPEUR2BABY") ,
        ];
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($args));

        $response = curl_exec($ch);

        curl_close($ch);

        //return $this->redirect($urlApi);
    }
}
