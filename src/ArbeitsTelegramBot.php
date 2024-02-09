<?php

namespace src;

use Telegram\Bot\Api;
use Symfony\Component\Dotenv\Dotenv;
use src\ArbeitsBotMenu;


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

        $this->parser = new Parser();

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

    public function debug($arr){
        file_put_contents(__DIR__ . '/test.txt',var_export($arr,1));
    }

    protected function handleMessage($message)
    {
        $messageText = $message['text'];
        $chatId = $message['chat']['id'];

        switch ($messageText) {
            case '/start':
                $this->menu->startMenu($chatId,$this->telegram);
                break;
            default:
                break;
        }
    }

    protected function handleCallbackQuery($callbackQuery)
    {
        $callbackData = $callbackQuery['data'];
        $chatId = $callbackQuery['message']['chat']['id'];

        if (strpos($callbackData, 'region_key_board') !== false) {
            $regionKey = str_replace('region_key_board ', '', $callbackData);
            $this->handleRegionMenu($chatId, $regionKey);
        }elseif (strpos($callbackData, 'city_key_board') !== false){

        }elseif (strpos($callbackData, 'platsbanken_next_') !== false){
            $platsbanken_next = (int)str_replace('platsbanken_next_', '', $callbackData);
            $this->menu->platsbankenShowAll($chatId,$this->telegram,$platsbanken_next+25);
        }elseif (strpos($callbackData, 'platsbanken_prev_') !== false){
            $platsbanken_prew = (int)str_replace('platsbanken_prev_', '', $callbackData);
            $this->menu->platsbankenShowAll($chatId,$this->telegram,$platsbanken_prew-25);
        } else {

            switch ($callbackData) {
                case 'platsbanken':
                    $this->menu->platsbankenMenu($chatId,$this->telegram);
                    break;
                case 'platsbanken_show_all':
                    $this->menu->platsbankenShowAll($chatId,$this->telegram);
                    break;
                default:
                    break;
            }
        }
    }
}
