<?php

namespace src;

require 'vendor/autoload.php';


class Parser
{

    public $url = 'https://platsbanken-api.arbetsformedlingen.se/jobs/v1/';

    public $offset;


    function getLists()
    {
        $offset = $this->makeApiRequest($this->url . 'search', [
            'source' => 'pb'
        ]);
        $offset = json_decode($offset, true)['offsetLimit'] / 25;

        $lists = 0;

        for ($i = 0; $i < $offset; $i++) {

            $dataId = $this->getIdPage($lists);

            $this->getDataSinglePage($dataId);


            $lists += 25;

        }

    }

    function getDataSinglePage($id)
    {

        foreach ($id as $value) {
            $getSingleData = $this->makeApiRequest($this->url . "job/" . $value['id']);
            $getSingleData = json_decode($getSingleData, true);

            $description = $this->findValueByKey('description', $getSingleData);
            $organisationNumber = $this->findValueByKey('organisationNumber', $getSingleData);
            var_dump($getSingleData);

            die();
        }
        die();

    }

    function getIdPage($lists)
    {

        $getPage = $this->makeApiRequest($this->url . 'search', [
            'startIndex' => $lists
        ]);
        $pageId = [];
        $page = json_decode($getPage, true)['ads'];

        foreach ($page as $value) {
            $pageId[] = [
                'id' => $value['id']
            ];

        }
        return $pageId;
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


    function findValueByKey($key, $array)
    {
        foreach ($array as $k => $value) {
            if ($k === $key) {
                return $value;
            } elseif (is_array($value)) {
                $result = $this->findValueByKey($key, $value);
                if ($result !== null) {
                    return $result;
                }
            }
        }
        return null; // Возвращаем null, если ключ не найден
    }

    function getCity(){
        $url = 'https://platsbanken-api.arbetsformedlingen.se/taxonomy/v1/trees?type=location';

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Возврат результата запроса в виде строки
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Отключение проверки SSL сертификата (не рекомендуется в рабочем окружении)

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Ошибка при выполнении запроса: ' . curl_error($ch);
        }

        curl_close($ch);

        return json_decode($response,true);
    }
}


