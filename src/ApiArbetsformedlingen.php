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

    public function showAll($startIndex, $cityId) {
        $filters = array();
        if ($cityId !== null) {
            $filters[] = array(
                "type" => "municipality",
                "value" => $cityId
            );
        }

        $getAll = $this->makeApiRequest($this->platsbanken_api_url.'search', array(
            "filters" => $filters,
            "fromDate" => null,
            "order" => "relevance",
            "maxRecords" => 5,
            "startIndex" => $startIndex,
            "toDate" => "2024-02-11T18:18:34.053Z",
            "source" => "pb"
        ));

        $getAll = json_decode($getAll, true);

        return $getAll;
    }

    public function getOne($id){
        $request = $this->makeApiRequest($this->platsbanken_api_url."job/$id");

        $request = json_decode($request,1);

        return $request;
    }

    public function getLocation(){
        $location = $this->makeApiRequest($_ENV['PLATS_URL_LOCATION']);
        if ($location){
            $location = json_decode($location,true);
            return $location;
        }
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
