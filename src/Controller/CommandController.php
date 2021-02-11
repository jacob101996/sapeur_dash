<?php

namespace App\Controller;

use App\Entity\Command;
use App\Repository\ProductRepository;
use App\Repository\StatusCommandRepository;
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
     * @return RedirectResponse
     * @throws \Exception
     */
    public function startCommand(SessionInterface $session, Request $request,
                                 ProductRepository $repository, StatusCommandRepository $statusCommandRepository){

        $panier             = $session->get('session_cart', []);
        $panierWithData     = [];


        foreach ($panier as $id => $quantity)
        {
            $panierWithData []  = [
                'product'       => $repository->find($id),
                'qte'           => $quantity
            ];
        }

        //dd($_SERVER['DOCUMENT_ROOT']."/logo.jpg");

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

        if ($request->isMethod('POST'))
        {
            $command        = new Command();
            $code           = date("dHis"); // JJHIS 01159652
            $date_cmd       = new \DateTime("now");

            // Info sur la commande
            $command->setTransId(substr(date('Y'), 2, 4).date("md").mt_rand(11,99));// Transaction id = AAMMJJ01
            $command->setRefCmd($code);
            $command->setMntTtc($total);
            $command->setMntHt($total);
            $command->setTauxTva(18);
            $command->setDateDelivery($date_cmd);

            // Calcul du montant TTC
            $MntTva =  ($command->getTauxTva() * $total)/100;
            $mntTtc = ($total + $MntTva);

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

            // Impression des la facture proforma
            $this->generateProformaInvoice($code, $command->getNameClt(), $command->getTelClt(), $command->getDeliveryLocation(),
                $date_cmd, $command->getBuyedBy(), $panierWithData, $total, $command->getTauxTva(), $mntTtc, "/var/www/html/sapeur2baby/public/logo.jpg");


            // Mise a jour du stock du produit dans la base de donnee
            foreach ($panierWithData as $item)
            {
                $newQte =   ($item['product']->getProductStock() - $item['qte']);
                $item['product']->setProductStock($newQte);
            }

            // Validation de la requette
            $this->em->flush();

            // Destruction de la session
            $session->remove("session_cart");

            return $this->redirectToRoute('cmd_congratulation');
        }

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
                                            $mnt_ttc, $logo)
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
            'tva'               => $tva
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
}
