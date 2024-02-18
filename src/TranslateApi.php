<?php

namespace src;

use Symfony\Component\Dotenv\Dotenv;

class TranslateApi
{

    public $url;

    public function __construct()
    {
        $env = new Dotenv();
        $env->load(__DIR__ . '/../.env');
    }

    public function translate($text,$language)
    {
        // Кодируем текст для URL
        $encoded_text = urlencode($text);

        // Формируем URL
        $url = $_ENV['TRANSLATE_URL'] . $encoded_text . "&sl=auto&tl=$language&hl=en";

        // Создаем cURL сессию
        $curl = curl_init();

        // Задаем параметры запроса
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Host: translate.google.com",
                "Accept: */*",
                "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3",
                "Accept-Encoding: gzip, deflate, br",
                "Content-type: application/x-www-form-urlencoded",
                "Alt-Used: translate.google.com",
                "Connection: keep-alive",
                // Добавляем куки с вашего предыдущего запроса
                "Sec-Fetch-Dest: empty",
                "Sec-Fetch-Mode: cors",
                "Sec-Fetch-Site: cross-site",
                "Referer: https://translate.google.com/"
            ),
        ));

        // Выполняем запрос и получаем ответ
        $response = curl_exec($curl);

        // Проверяем на наличие ошибок
        if (curl_errno($curl)) {
            echo 'Ошибка cURL: ' . curl_error($curl);
        }

        // Закрываем cURL сессию
        curl_close($curl);

        $response = json_decode($response, true);

        return $this->extractTranslations($response);
    }

    public function extractTranslations($response)
    {
        $translations = [];

        // Проверяем, что в ответе есть массив 'sentences'
        if (isset($response['sentences'])) {
            foreach ($response['sentences'] as $sentence) {
                // Получаем перевод из элемента массива 'trans'
                $translation = $sentence['trans'];

                // Добавляем перевод в результирующий массив
                $translations[] = $translation;
            }
        }
        $translationStr = '';
        foreach ($translations as $translation) {
            $translationStr .= $translation . "<br>";
        }
        return $translationStr;
    }
}


