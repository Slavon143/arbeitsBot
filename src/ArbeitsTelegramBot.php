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
    protected $parser;

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

        if ($this->isJson($callbackData)) {
            $callbackData = json_decode($callbackData, true);
        }

        switch (true) {
            case array_key_exists('platsbanken', $callbackData):
                $menu->platsbankenMenu($chatId, $this->telegram);
                break;
            case array_key_exists('platsbanken_show_all', $callbackData):
                $menu->platsbankenShowAll($chatId, $this->telegram);
                break;
            case array_key_exists('platsbanken_next', $callbackData):
                $offset = $callbackData['page'];
                $sityId = $callbackData['city_id'];
                if ($offset === null) {
                    $offset += 5;
                }
                $this->debug($callbackQuery);
                $menu->platsbankenShowAll($chatId, $this->telegram, $offset,$sityId);
                break;
            case array_key_exists('platsbanken_prev', $callbackData):
                $offset = $callbackData['page'];
                $sityId = $callbackData['city_id'];
              if($offset >= 5){
                  $offset -= 5;
              }
                $menu->platsbankenShowAll($chatId, $this->telegram, $offset,$sityId);
                break;
            case array_key_exists('platsbanken_filter', $callbackData):
                $menu->platsbankenFilter($chatId, $this->telegram);
                break;
            case array_key_exists('show_detail_page', $callbackData):
                $detail_id = $callbackData['detail_id'];
                $menu->showOne($chatId, $this->telegram, $detail_id);
                break;
            case array_key_exists('platsbanken_filter_ort', $callbackData):
                $menu->showRegion($chatId, $this->telegram);
                break;
            case array_key_exists('filter_region_id', $callbackData):
                $region_id = $callbackData['filter_region_id'];
                $menu->showCity($chatId, $this->telegram, $region_id);
                break;
            case array_key_exists('filter_city_id', $callbackData):
                $filter_city_id = $callbackData['filter_city_id'];
                $menu->showFilterChose($chatId, $this->telegram, $filter_city_id);
                break;
            case array_key_exists('show_all_filter_city', $callbackData):
                $city_id = $callbackData['city_id'];
                $menu->platsbankenShowAll($chatId, $this->telegram, null,$city_id);
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
