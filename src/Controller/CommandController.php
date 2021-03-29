<?php

namespace App\Controller;

use App\Entity\Command;
use App\Entity\StatusCommand;
use App\Repository\CategoryProductRepository;
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
                                 CategoryProductRepository $categoryProductRepository){


        $panier             = $session->get('session_cart', []);

        $panierWithData     = [];

        foreach ($panier as $id => $quantity) {
            $panierWithData []  = [
                'product'       => $repository->find($id),
                'qte'           => $quantity
            ];
        }

        // Recuperation de la reponse du server
        $response = $request->get('responsecode');


        if (!is_null($response) && intval($response) == 0)
        {

            $chanel = $request->get('channel');
            $refNumber = $request->get('referenceNumber');
            $transAt = $request->get('transactiondt');
            $amount  = $request->get('amount');
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

                // Validation de la requette
                $this->em->flush();

                // Destruction de la session
                $session->remove("session_cart");

                return $this->redirectToRoute('congratulation', [
                    'code' => intval($response), 'chanel' => $chanel, 'refNumber' => $refNumber,
                    'transAt' => $transAt, 'amount' => $amount, 'idTrans' => $idTrans
                ]);
            }

        }

        $total      = null;
        $prix       = null;

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


        if ($request->isMethod('POST'))
        {
            $command        = new Command();
            $code           = "01".date("dm").substr(date('Y'), 2, 4); // Ref 01JJMMAA 01 Code debut SAPEUR2BABY
            $date_cmd       = new \DateTime("now");

            // Info sur la commande
            $command->setTransId(date("His").mt_rand(11,99));// Transaction id = HMSC
            $command->setRefCmd($code);
            $command->setMntTtc($total);
            $command->setMntHt($total);
            $command->setTauxTva(0);
            $command->setDateDelivery($date_cmd);

            // Calcul du montant TTC
            $MntTva =  ($command->getTauxTva() * $total)/100;
            $mntTtc = round($total + $MntTva);

            // Calcul des dix pourcent
            $dix_pourcent   = ($mntTtc * 10)/100;

            $command->setMntTtc($mntTtc);
            $command->setStatus($statusCommandRepository->find(1));

            // Produit
            foreach ($panierWithData as $item){
                $command->addProduct($item['product']);
            }

            // infos client
            $command->setNameClt($request->get('name'));
            $command->setTelClt($request->get('contact'));
            $command->setDeliveryLocation($request->get('lieu'));
            $command->setBuyedBy($request->get('buy'));
            $command->setCommandAt($date_cmd);
            $command->setFactureName($code.'.pdf');

            // Persistence des donnees
            $this->em->persist($command);

            /*===============================================================*/
            //                        Payment Pro Api                        //
            /*===============================================================*/

            // Appel du service de payment
            $paymentPro =   new PaymentPro();
            $sessionId = $paymentPro->executePayment($dix_pourcent, 1, $command->getNameClt(),
                $command->getNameClt(), $command->getTelClt(), $command->getBuyedBy(), $command->getRefCmd());

            //dd($sessionId);

            if (!is_null($sessionId))
            {
                try {
                    // Recuperation de la session
                    $array = (array)$sessionId;

                    // Impression des la facture proforma
                    $this->generateProformaInvoice($code, $command->getNameClt(), $command->getTelClt(), $command->getDeliveryLocation(),
                        $date_cmd, $command->getBuyedBy(), $panierWithData, $total, $command->getTauxTva(), $mntTtc,$dix_pourcent ,"/var/www/html/sapeur2baby/public/logo.jpg");

                    $this->em->flush();

                    // Redirect to API Payment PRO
                    return $this->redirect('https://paiementpro.net/webservice/onlinepayment/processing_v2.php?sessionid='
                        .$array['Sessionid'], 307);

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
     * @param $tva
     * @param $mnt_ttc
     * @param $logo
     */
    public function generateProformaInvoice($code, $name, $tel, $lieu, $date_cmd, $moyen_buy, $panierWithData, $sub_total, $tva,
                                            $mnt_ttc,$dix_pourcent, $logo)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Courier');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $txt    = $name."\r\n".$tel."\r\n".$mnt_ttc."\r\n".date('Y/m/m H:i:s');


        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('invoice/proforma_invoice.html.twig', [
            'numero'            => $code,
            'items'             => $panierWithData,
            'sub_total'         => $sub_total,
            'date_cmd'          => $date_cmd,
            'name'              => $name,
            'tel'               => $tel,
            'lieu'              => $lieu,
            'qr_code'           => $this->generateQRCode($txt),
            'logo'              => $logo,
            'mnt_ttc'           => $mnt_ttc,
            'moyen_buy'         => $moyen_buy,
            'dix_pourcent'      => $dix_pourcent,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

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
        $pdfFilepath =  $publicDirectory .$code.'.pdf';

        // Write file to the desired path
        file_put_contents($pdfFilepath, $output);
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
