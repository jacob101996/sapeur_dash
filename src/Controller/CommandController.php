<?php

namespace App\Controller;

use App\Entity\Command;
use App\Entity\StatusCommand;
use App\Repository\CategoryProductRepository;
use App\Repository\CommandRepository;
use App\Repository\ProductRepository;
use App\Repository\StatusCommandRepository;
use App\Services\PaymentPro;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class CommandController extends AbstractController
{
    private  $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em   = $em;
    }

    /**
     * @Route("/start-command", name="start_command")
     * @param SessionInterface $session
     * @param Request $request
     * @param ProductRepository $repository
     * @param StatusCommandRepository $statusCommandRepository
     * @param CategoryProductRepository $categoryProductRepository
     * @return Response
     * @throws \SoapFault
     */
    public function startCommand(SessionInterface $session, Request $request,
                                 ProductRepository $repository,
                                 StatusCommandRepository $statusCommandRepository,
                                 CategoryProductRepository $categoryProductRepository,
                                 CommandRepository $commandRepository){


        $panier             = $session->get('session_cart', []);

        $panierWithData     = [];

        foreach ($panier as $id => $quantity) {
            $panierWithData []  = [
                'product'       => $repository->find($id),
                'qte'           => $quantity
            ];
        }


        $total              = null;
        $prix               = null;
        $amountBuyed        = null;

        foreach ($panierWithData as $item) {

            if (is_null($item['product']->getProductReduction())){
                $prix   = $item['product']->getProductPrice();
            }else{
                $reductionPrice = ($item['product']->getProductPrice() * $item['product']->getProductReduction())/100;
                $prix   = (intval($item['product']->getProductPrice()) + intval($reductionPrice));
            }

            $totalItems = ($prix * $item['qte']);
            $total      += $totalItems;
        }

        // Count nbr item in panier
        $tabQte = [];
        foreach ($panier as $id => $quantity)
        {
            $tabQte []  = $quantity;
        }

        // Recuperation de la reponse du server
        $response = $request->get('responsecode');

        if (!is_null($response) && intval($response) == 0)
        {
            $chanel = $request->get('channel');
            $refNumber = $request->get('referenceNumber');
            $transAt = $request->get('transactiondt');
            $amount  = $request->get('buy');
            $idTrans = $request->get('payId');

            // Recuperation de la commande par la reference
            $command = $this->em->getRepository(Command::class)->findByRefCmd($request->get('referenceNumber'));

            if (count($command) > 0)
            {
                // Mise a jour du stock du produit dans la base de donnee
                foreach ($panierWithData as $item) {
                    $newQte =   ($item['product']->getProductStock() - $item['qte']);
                    $item['product']->setProductStock($newQte);
                    // Validation de la requette
                    $this->em->flush();
                }

                $command[0]->setPayId($request->get('payId'));
                $command[0]->setStatus($this->em->getRepository(StatusCommand::class)->find(2));
                $command[0]->setEtat("success");
                $command[0]->setIsBuyed(true);

                // Validation de la requette
                $this->em->flush();

                // Destruction de la session
                $session->remove("session_cart");

                return $this->redirectToRoute('congratulation', [
                    'code' => intval($response), 'chanel' => $chanel, 'refNumber' => $refNumber,
                    'transAt' => $transAt, 'amount' => $amount, 'idTrans' => $idTrans
                ]);
            }

        }elseif (!is_null($response) && intval($response) != 0){
            $command = $this->em->getRepository(Command::class)->findByRefCmd($request->get('referenceNumber'));
            $command[0]->setEtat("echec");
            $command[0]->setIsBuyed(false);
            $this->em->flush();
        }

        if ($request->isMethod('POST'))
        {
            $command        = new Command();
            $code           = "01".date("His"); // Ref 01HMS 01 Code debut SAPEUR2BABY
            $date_cmd       = new \DateTime("now");

            $dateLivraison  = $date_cmd->add(\DateInterval::createFromDateString('+ '.$request->get('mode_livraison')." days"));

            // Nom de la facture
            $lastCmd    = $commandRepository->findByLastCmd();

            if (count($lastCmd) > 0){
                $lastNumero     = $lastCmd[0]->getNumberFacture();
                $numeroFactureIncrement = "000".(intval($lastNumero) + 1);
            }else
                $numeroFactureIncrement = "0001";

            $command->setNumberFacture($numeroFactureIncrement);

            // Info sur la commande
            $command->setTransId(date("His").mt_rand(11,99));// Transaction id = HMSC
            $command->setRefCmd($code);
            $command->setMntTtc($total);
            $command->setMntHt($total);
            $command->setTauxTva(0);
            $command->setDateDelivery($dateLivraison);
            $command->setIsBuyed(false);
            //Defini la date de fin

            // Calcul du montant TTC
            $MntTva =  ($command->getTauxTva() * $total)/100;
            $mntTtc = round($total + $MntTva);
            $amountLivraison = 0;

            if ($mntTtc >= 50000) {
                // Calcul des dix pourcent
                $amountBuyed   = $mntTtc;
                $amountLivraison = 0;
            }else{
                if ($request->get('point_livraison') == "Abidjan"){
                    $amountBuyed   = 5;
                    $amountLivraison = 5;
                }else{
                    $amountBuyed   = 10;
                    $amountLivraison = 10;
                }
            }

            $command->setMontantBuy($amountBuyed);
            $command->setMontantLivraison($amountLivraison);

            $command->setMntTtc($mntTtc);
            $command->setStatus($statusCommandRepository->find(1));

            // Produit
            foreach ($panierWithData as $item){
                $command->addProduct($item['product']);
            }

            $lieuLivraison  = $request->get('point_livraison').",".$request->get('commune')
                .",".$request->get('lieu');

            $commandAt  = new \DateTime("now");

            // infos client
            $command->setNameClt($request->get('name'));
            $command->setTelClt($request->get('contact'));
            $command->setDeliveryLocation($lieuLivraison);
            $command->setBuyedBy($request->get('buy'));
            $command->setCommandAt($commandAt);
            $command->setFactureName("FAC-".$numeroFactureIncrement.'.pdf');
            $command->setEtat("echec");

            // Persistence des donnees
            $this->em->persist($command);

            /*===============================================================*/
            //                        Payment Pro Api                        //
            /*===============================================================*/

            // Appel du service de payment
            $paymentPro =   new PaymentPro();
            $sessionId = $paymentPro->executePayment($amountBuyed, 1, $command->getNameClt(),
                $command->getNameClt(), $command->getTelClt(), $command->getBuyedBy(), $command->getRefCmd());

            //dump($command->getBuyedBy());

            //dd($sessionId->Sessionid);

            if (!is_null($sessionId))
            {
                try {
                    // Recuperation de la session
                    //$array = (array)$sessionId;

                    // Impression des la facture proforma
                    $this->generateProformaInvoice($numeroFactureIncrement, $command->getNameClt(), $command->getTelClt(),
                        $command->getDeliveryLocation(), $commandAt->format('d/m/Y H:i:s'), $command->getBuyedBy(), $panierWithData, $total,
                        $mntTtc, $amountBuyed, $dateLivraison->format('d/m/Y'), $amountLivraison, $code);

                    $this->em->flush();

                    if ($command->getBuyedBy() == "OMCIV2"){
                        // Redirect to API Payment PRO
                        return $this->redirect('https://www.paiementpro.net/webservice/onlinepayment/processing_v2.php?sessionid='
                            .$sessionId->Sessionid);
                    }else{
                        // Redirect to API Payment PRO
                        return $this->redirect('https://www.paiementpro.net/webservice/onlinepayment/processing_v2.php?sessionid='
                            .$sessionId->Sessionid, 307);
                    }


                    //header("Location:https://www.paiementpro.net/webservice/onlinepayment/processing_v2.php?sessionid=".$sessionId->Sessionid);

                }catch (\SoapFault $fault) {
                    die($fault->getMessage());
                }catch (\Exception $e){
                    die($e->getMessage());
                }
            }

            /*===============================================================*/
            //                        Fin Payment Pro Api                    //
            /*===============================================================*/

        }


        return $this->render('home/my_cart.html.twig', [
            'products'                => $repository->findManiProductItems(),
            'nbprod'                  => array_sum($tabQte),
            'cats'                    => $categoryProductRepository->findAll(),
            'product_session'         => $panierWithData,
            'total'                   => $total,
            'nbfavori'                => count($session->get('session_heart', [])),
        ]);

    }

    /**
     * @param $code
     * @param $name
     * @param $tel
     * @param $lieu
     * @param $date_cmd
     * @param $moyen_buy
     * @param $panierWithData
     * @param $sub_total
     * @param $mnt_ttc
     * @param $amountBuyed
     * @param $dateLivraison
     * @param $amountLivraison
     * @param $ref
     */
    public function generateProformaInvoice($code, $name, $tel, $lieu, $date_cmd, $moyen_buy, $panierWithData, $sub_total,
                                            $mnt_ttc,$amountBuyed, $dateLivraison, $amountLivraison, $ref)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Courier');
        $pdfOptions->setIsRemoteEnabled(true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $dompdf->setOptions($pdfOptions);

        $qr    = $name."\r\n".$tel."\r\n".$dateLivraison;


        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('invoice/proforma_invoice.html.twig', [
            'numero'            => $code,
            'items'             => $panierWithData,
            'sub_total'         => $sub_total,
            'date_cmd'          => $date_cmd,
            'name'              => $name,
            'tel'               => $tel,
            'lieu'              => $lieu,
            'qr_code'           => $this->generateQRCode($qr),
            'logo'              => "http://sapeurdebaby.piecesivoire.com/bootstrap/images/logo.png",
            'mnt_ttc'           => $mnt_ttc,
            'moyen_buy'         => $moyen_buy,
            'amount_buyed'      => $amountBuyed,
            'date_livraison'    => $dateLivraison,
            'amount_livraison'  => $amountLivraison,
            'montant_total_fac' => ($amountLivraison + $mnt_ttc),
            'ref'               => $ref,


        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('B5', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Store PDF Binary Data
        $output = $dompdf->output();

        // In this case, we want to write the file in the public directory
        $publicDirectory = $_ENV['DIR_NAME'].'/invoice/';
        // Creation du repectoire si il n'existe pas

        if (!file_exists($publicDirectory)){
            mkdir($publicDirectory, 0777, true);
        }

        // e.g /var/www/project/public/mypdf.pdf
        $pdfFilepath =  $publicDirectory ."FAC-".$code.'.pdf';

        // Write file to the desired path
        file_put_contents($pdfFilepath, $output);
    }

    /**
     * @Route("/facture/test")
     */
    public function textFacture(){

        $this->generateProformaInvoice("0001", "Zouma Onesime", "0153686832",
            "Abidjan,Cocody, Angré", date("Y/m/d H:i:s"), "Moov Money", "",
            11000, 20000, 1500, date("Y/m/d"), 1500, "43423243");

        echo "Succès";

    }

    /**
     * @param $text
     * @return string
     */
    public function generateQRCode($text)
    {
        // Initialisation du QR Code
        $qrCode = new QrCode($text);
        header('Content-Type :'.$qrCode->getContentType());
        $qrCode->setSize(250);
        $qrCode->setMargin(10);

        // Set advanced options
        $qrCode->setWriterByName('png');
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH());
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
        $qrCode->setValidateResult(false);
        return $qrCode->writeDataUri();
    }

    /**
     * @Route("/congratulation/{code}", name="congratulation")
     * @param $code
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function congratulation($code, Request $request)
    {

        if ( intval($code) == -1){
            $this->addFlash("warning", "Ouff! Un problème est survenu lors de l'opération, veuillez reprendre ultérieurement");
            return $this->redirectToRoute("my_cart");

        } // Erreur initialisation
        elseif (intval($code) == 10){
            $this->addFlash("warning", "Ouff! Un problème est survenu lors de l'opération, veuillez reprendre ultérieurement");
            return $this->redirectToRoute("my_cart");

        } // Paramètres insuffisants
        elseif (intval($code) == 11){
            $this->addFlash("warning", "Ouff! Un problème inconu est survenu lors de l'opération, veuillez reprendre ultérieurement");
            return $this->redirectToRoute("my_cart");

        } // ID marchand inconnu

        $command =  $this->em->getRepository(Command::class)->findByPayId($request->get('idTrans'));

        if (count($command) == 0) {
            $this->addFlash("warning", "Ouff ! Une erreur inconu est survenu en cas de 
            problème merci de contacter notre service technique");

            return $this->redirectToRoute("error", ['code' => $this->generateToken()]);
        }

        return $this->render('home/congratulation.html.twig', [
            "nbfavori"       => 0,
            "nbprod"         => 0,
            "chanel"         => $request->get('chanel'),
            "ref"            => $request->get('refNumber'),
            "trans_at"       => $request->get('transAt'),
            "amount"         => $request->get('amount'),
            "idTrans"        => $request->get('idTrans'),
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    /**
     * @Route("/error/{code}", name="error")
     * @return Response
     */
    public function error()
    {
        return $this->render('home/error.html.twig', [
            "nbfavori"       => 0,
            "nbprod"         => 0,
        ]);
    }
}
