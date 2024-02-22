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

    public function showAll($startIndex,$resource,$param) {
        $filters = [];
        if (isset($param['c_id'])) {
            $filters[] = [
                "type" => "municipality",
                "value" => $param['c_id']
            ];
        }
        if (isset($param['spec_all'])) {
            $filters[] = [
                "type" => "occupationField",
                "value" => $param['spec_all']
            ];
        }
        if (isset($param['r_id'])) {
            $filters[] = [
                "type" => "region",
                "value" => $param['r_id']
            ];
        }
        if (isset($param['spec_id'])) {
            $filters[] = [
                "type" => "occupationGroup",
                "value" => $param['spec_id']
            ];
        }
        if (isset($param['se_t'])){
            $filters[] = [
                "type" => "freetext",
                "value" => $param['se_t']
            ];
        }
        $requestData = [
            "filters" => $filters,
            "fromDate" => null,
            "order" => "relevance",
            "maxRecords" => 5,
            "startIndex" => $startIndex,
            "source" => $resource
        ];

        $getAll = $this->makeApiRequest($this->platsbanken_api_url.'search', $requestData);

        $getAll = json_decode($getAll, true);

        return $getAll;
    }

    public function getOne($id,$resource){
        if ($resource == 'pb'){
            $request = $this->makeApiRequest($this->platsbanken_api_url."job/$id");
        }else{
            $request = $this->makeApiRequest($this->platsbanken_api_url."job/$resource/$id");
        }
        $request = json_decode($request,true);

        return $request;
    }

    public function getOccupation(){
        $occupation = $this->makeApiRequest($_ENV['PLATS_URL_OCCUPAYION']);

        if ($occupation){
            $occupation = json_decode($occupation,true);
            return $occupation;
        }
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

