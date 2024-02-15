<?php

namespace src;

use Telegram\Bot\Api;
use Symfony\Component\Dotenv\Dotenv;
use src\ActionHandler;

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
        // Инициализация ActionHandler
        $actionHandler = new ActionHandler('histories');

// Ваш switch/case
        switch (true) {
            case array_key_exists('translate', $callbackData):
                $translate_id = $callbackData['translate'];
                $actionHandler->addToHistory($chatId, ['type' => 'translate', 'translate_id' => $translate_id]);
                $this->menu->showOneTranslate($chatId, $this->telegram, $translate_id);
                break;
            case array_key_exists('platsbanken', $callbackData):
                $actionHandler->addToHistory($chatId, ['type' => 'platsbanken']);
                $menu->showRegion($chatId, $this->telegram);
                break;
            case array_key_exists('show_city', $callbackData):
                $region_id = $callbackData['region_id'];
                $actionHandler->addToHistory($chatId, ['type' => 'show_city', 'region_id' => $region_id]);
                $menu->showCity($chatId, $this->telegram, $region_id);
                break;
            case array_key_exists('show_occupation', $callbackData):
                $city_id = $callbackData['city_id'];
                $actionHandler->addToHistory($chatId, ['type' => 'show_occupation', 'city_id' => $city_id]);
                $menu->platsbankenShowOccupation($chatId, $this->telegram, $city_id);
                break;
            case array_key_exists('translate_occupation', $callbackData):
                $city_id = $callbackData['translate_occupation'];
                $actionHandler->addToHistory($chatId, ['type' => 'translate_occupation', 'city_id' => $city_id]);
                $menu->platsbankenShowOccupation($chatId, $this->telegram, $city_id, true);
                break;
            case array_key_exists('show_specialist', $callbackData):
                $city_id = $callbackData['city_id'];
                $occupation_id = $callbackData['show_specialist'];
                $actionHandler->addToHistory($chatId, ['type' => 'show_specialist', 'city_id' => $city_id, 'occupation_id' => $occupation_id]);
                $this->menu->platsbankenShowOccupationClass($chatId, $this->telegram, $occupation_id, $city_id);
                break;
            case array_key_exists('translate_specialist', $callbackData):
                $occupation_id = $callbackData['translate_specialist'];
                $city_id = $callbackData['city_id'];
                $actionHandler->addToHistory($chatId, ['type' => 'translate_specialist', 'city_id' => $city_id, 'occupation_id' => $occupation_id]);
                $this->menu->platsbankenShowTranslateSpecialist($chatId, $this->telegram, $occupation_id, $city_id, true);
                break;
            case array_key_exists('show_profession', $callbackData):
                $specialist_id = $callbackData['show_profession'];
                $city_id = $callbackData['city_id'];
                $actionHandler->addToHistory($chatId, ['type' => 'show_profession', 'city_id' => $city_id, 'specialist_id' => $specialist_id]);
                $this->menu->showResult($chatId, $this->telegram, $specialist_id, $city_id, $startIndex = null);
                break;
            case array_key_exists('show_detail_page', $callbackData):
                $detail_id = $callbackData['detail_id'];
                $actionHandler->addToHistory($chatId, ['type' => 'show_detail_page', 'detail_id' => $detail_id]);
                $this->menu->showOne($chatId, $this->telegram, $detail_id);
                break;
            case array_key_exists('forward_page', $callbackData):
                $cityId = $callbackData['ci'];
                $specialist_id = $callbackData['spec'];
                $page = $callbackData['forward_page'];
                $actionHandler->addToHistory($chatId, ['type' => 'forward_page', 'city_id' => $cityId, 'specialist_id' => $specialist_id, 'page' => $page]);
                $this->menu->showResult($chatId, $this->telegram, $specialist_id, $cityId, $page);
                break;
            case array_key_exists('back_page', $callbackData):
                $cityId = $callbackData['ci'];
                $specialist_id = $callbackData['spec'];
                $page = $callbackData['back_page'];
                $actionHandler->addToHistory($chatId, ['type' => 'back_page', 'city_id' => $cityId, 'specialist_id' => $specialist_id, 'page' => $page]);
                $this->menu->showResult($chatId, $this->telegram, $specialist_id, $cityId, $page);
                break;
            default:
                break;
        }
        if (array_key_exists('back', $callbackData)){
            $previousAction = $actionHandler->getPreviousAction($chatId);

            if ($previousAction) {
                switch ($previousAction['type']) {
                    case 'translate':
                        $this->menu->showOneTranslate($chatId, $this->telegram, $previousAction['translate_id']);
                        $actionHandler->removeLastAction($chatId);
                        break;
                    case 'platsbanken':
                        $menu->showRegion($chatId, $this->telegram);
                        $actionHandler->removeLastAction($chatId);
                        break;
                    case 'show_city':
                        $menu->showCity($chatId, $this->telegram, $previousAction['region_id']);
                        $actionHandler->removeLastAction($chatId);
                        break;
                    case 'show_occupation':
                        $menu->platsbankenShowOccupation($chatId, $this->telegram, $previousAction['city_id']);
                        $actionHandler->removeLastAction($chatId);
                        break;
                    case 'translate_occupation':
                        $menu->platsbankenShowOccupation($chatId, $this->telegram, $previousAction['city_id'], true);
                        $actionHandler->removeLastAction($chatId);
                        break;
                    case 'show_specialist':
                        $menu->platsbankenShowOccupationClass($chatId, $this->telegram, $previousAction['occupation_id'], $previousAction['city_id']);
                        $actionHandler->removeLastAction($chatId);
                        break;
                    case 'translate_specialist':
                        $menu->platsbankenShowTranslateSpecialist($chatId, $this->telegram, $previousAction['occupation_id'], $previousAction['city_id'], true);
                        $actionHandler->removeLastAction($chatId);
                        break;
                    case 'show_profession':
                        $this->menu->showResult($chatId, $this->telegram, $previousAction['specialist_id'], $previousAction['city_id'], $startIndex = null);
                        $actionHandler->removeLastAction($chatId);
                        break;
                    case 'show_detail_page':
                        $this->menu->showOne($chatId, $this->telegram, $previousAction['detail_id']);
                        $actionHandler->removeLastAction($chatId);
                        break;
                    case 'forward_page':
                        $this->menu->showResult($chatId, $this->telegram, $previousAction['specialist_id'], $previousAction['city_id'], $previousAction['page']);
                        break;
                    case 'back_page':
                        $this->menu->showResult($chatId, $this->telegram, $previousAction['specialist_id'], $previousAction['city_id'], $previousAction['page']);
                        $actionHandler->removeLastAction($chatId);
                        break;
                    default:
                        break;
                }
            } else {
                // В случае, если история пуста или не существует
                $menu->showRegion($chatId, $this->telegram);
            }
        }
    }

    function isJson($string)
    {
        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }


}
