<?php


namespace App\Services;


use SoapFault;

ini_set("soap.wsdl_cache_enabled", 0);

class PaymentPro
{
    /**
     * @param $amount
     * @param $id
     * @param $name
     * @param $lastName
     * @param $contact
     * @param $modePay
     * @param $ref
     * @return mixed
     * @throws SoapFault
     */
    public function executePayment($amount, $id, $name, $lastName, $contact , $modePay, $ref)
    {

        $marchanID  = 'PP-F109';
        $notifUrl   = 'http://127.0.0.1:8000/start-command'; // http://sapeurdebaby.piecesivoire.com/start-command
        $returnUrl  = 'http://127.0.0.1:8000/congratulation'; // http://sapeurdebaby.piecesivoire.com//congratulation
        $urlApi     = "https://www.paiementpro.net/webservice/OnlineServicePayment_v2.php?wsdl";


        $client  = new \SoapClient($urlApi,array('cache_wsdl' => WSDL_CACHE_NONE));

        // Recuperation des infos relatives a l'utilisation de l'api
        $array  = array(
            'merchantId'            => $marchanID,
            'countryCurrencyCode'   => 952,
            'amount'                => $amount,
            'customerId'            => $id,
            'channel'               => $modePay,
            'customerEmail'         => "piecesivoire@gmail.com",
            'customerFirstName'     => $name,
            'customerLastname'      => $lastName,
            'customerPhoneNumber'   => $contact,
            'referenceNumber'       => $ref,
            'notificationURL'       => $notifUrl,
            'returnURL'             => $returnUrl,
            'description'           => 'Payment online sapeur2baby',
            'returnContext'         => 'error',
        );

        try{
            return $client->initTransact($array);
        }catch (\Exception $e)
        {
            die($e->getMessage());
        }

    }
}