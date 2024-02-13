<?php

namespace src;

use Telegram\Bot\Api;
use Symfony\Component\Dotenv\Dotenv;
use src\ArbeitsBotMenu;
use Telegram\Bot\Objects\Message;

class ArbeitsTelegramBot
{
    protected $token;
    protected $incomingRequest;
    protected $telegram;

    protected $menu;

    public function __construct()
    {
        $env = new Dotenv();
        $env->load(__DIR__ . '/../.env');
        $this->token = $_ENV['TELEGRAM_BOT_TOKEN'];

        $this->incomingRequest = json_decode(file_get_contents('php://input'), true);
        $this->telegram = new Api($this->token);

        $this->menu = new \src\ArbeitsBotMenu();
    }

    public function listen()
    {
        $update = json_decode(file_get_contents('php://input'), true);
        $message = $update['message'] ?? null;
        $callbackQuery = $update['callback_query'] ?? null;

        if ($message) {
            $this->handleMessage($message);
        } elseif ($callbackQuery) {
            $this->handleCallbackQuery($callbackQuery);
        }
    }

    public function debug($arr)
    {
        file_put_contents(__DIR__ . '/test.txt', var_export($arr, 1));
    }

    protected function handleMessage($message)
    {
        $messageText = $message['text'];
        $chatId = $message['chat']['id'];

        switch ($messageText) {
            case '/start':
                $this->menu->startMenu($chatId, $this->telegram);
                break;
            default:
                break;
        }
    }

    protected function handleCallbackQuery($callbackQuery)
    {
        $callbackData = $callbackQuery['data'];
        $chatId = $callbackQuery['message']['chat']['id'];
        $menu = $this->menu;
        $messageId = $callbackQuery['message']['message_id'];
        if ($this->isJson($callbackData)) {
            $callbackData = json_decode($callbackData, true);
        }

        switch (true) {


            case array_key_exists('translate', $callbackData):

                $translate_id = $callbackData['translate'];
                $this->menu->showOneTranslate($chatId, $this->telegram, $translate_id);

                break;

            case array_key_exists('platsbanken', $callbackData):
                $menu->showRegion($chatId, $this->telegram);
                break;
            case array_key_exists('show_city', $callbackData):
                $region_id = $callbackData['region_id'];
                $menu->showCity($chatId, $this->telegram, $region_id);
                break;
            case array_key_exists('show_occupation', $callbackData):
                $city_id = $callbackData['city_id'];
                $menu->platsbankenShowOccupation($chatId, $this->telegram,$city_id);
                break;
            case array_key_exists('show_specialist', $callbackData):
                $city_id = $callbackData['city_id'];
                $occupation_id = $callbackData['show_specialist'];
                $this->menu->platsbankenShowOccupationClass($chatId, $this->telegram,$occupation_id,$city_id);
                break;
            case array_key_exists('show_profession', $callbackData):
                $specialist_id = $callbackData['show_profession'];
                $city_id = $callbackData['city_id'];
                $this->menu->showResult($chatId, $this->telegram,$specialist_id,$city_id,$startIndex = null);
                break;
            case array_key_exists('show_detail_page', $callbackData):
                $detail_id = $callbackData['detail_id'];
                $this->menu->showOne($chatId,$this->telegram,$detail_id);
                break;
            case array_key_exists('forward_page', $callbackData):
                $sityId = $callbackData['ci'];
                $specialist_id = $callbackData['spec'];
                $page = $callbackData['forward_page'];
                $this->menu->showResult($chatId, $this->telegram,$specialist_id,$sityId,$page);
                break;
            case array_key_exists('back_page', $callbackData):
                $sityId = $callbackData['ci'];
                $specialist_id = $callbackData['spec'];
                $page = $callbackData['back_page'];
                $this->menu->showResult($chatId, $this->telegram,$specialist_id,$sityId,$page);
                break;
            default:
                break;
        }
    }

    function isJson($string)
    {
        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }


}
