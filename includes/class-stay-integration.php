<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * StayApp SA_Integration.
 *
 * Integration with webservice.
 *
 * @class    SA_Integration
 * @package  SA_Integration/Classes
 * @category Class
 * @author   StayApp
 */
class SA_Integration
{
    private $token;

    const WEBSERVICE_PATH = "https://api.stayapp.com.br";

    public function __construct($token){
        $this->token = $token;
    }

    public function getTickets(){
        return $this->sendRequest("/integration/tickets", []);
    }

    public function addStamp(array $stamp){

    }

    public function sendRequest($route, $data){
        $call = curl_init();
        curl_setopt($call, CURLOPT_URL, self::WEBSERVICE_PATH . $route);
        curl_setopt($call, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($call, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($call, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($call, CURLOPT_HTTPHEADER, array(
            'Token: ' . $this->token
        ));
        return curl_exec($call);
    }
}