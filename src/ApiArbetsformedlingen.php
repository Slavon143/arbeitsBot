<?php

namespace src;
use Symfony\Component\Dotenv\Dotenv;


class ApiArbetsformedlingen
{

    public $platsbanken_api_url;

    public function __construct(){
        $env = new Dotenv();
        $env->load(__DIR__ . '/../.env');

        $this->platsbanken_api_url = $_ENV['PLATS_URL'];
    }

    function makeApiRequest($url, $data = [])
    {
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
        ];

        if (!empty($data)) {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
            $options[CURLOPT_HTTPHEADER] = [
                'Content-Type: application/json',
            ];
        }
        $curl = curl_init();
        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);

        if ($response === false) {
            echo 'Curl error: ' . curl_error($curl);
        } else {
            return $response;
        }

        curl_close($curl);
    }

}
