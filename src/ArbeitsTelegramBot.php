<?php

namespace src;

use src\ActionHandler;

use Telegram\Bot\Api;
use Symfony\Component\Dotenv\Dotenv;


class ArbeitsTelegramBot
{
    protected $token;
    protected $incomingRequest;
    protected $telegram;

    protected $menu;

    protected $actionHandler;

    public function __construct()
    {
        $env = new Dotenv();
        $env->load(__DIR__ . '/../.env');
        $this->token = $_ENV['TELEGRAM_BOT_TOKEN'];

        $this->incomingRequest = json_decode(file_get_contents('php://input'), true);
        $this->telegram = new Api($this->token);

        $this->menu = new \src\ArbeitsBotMenu();

        $this->actionHandler = new ActionHandler('histories');
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
                break;
            case '/back':
                $previousAction = $this->actionHandler->getPreviousAction($chatId);
                switch ($previousAction['type']) {
                    case 'translate':
                        $this->menu->showOneTranslate($chatId, $this->telegram, $previousAction['translate_id']);
                        $this->actionHandler->removeLastAction($chatId);
                        break;
                    case 'platsbanken':
                        $this->menu->showRegion($chatId, $this->telegram);
                        $this->actionHandler->removeLastAction($chatId);
                        break;
                    case 'show_city':
                        $this->menu->showCity($chatId, $this->telegram, $previousAction['region_id']);
                        $this->actionHandler->removeLastAction($chatId);
                        break;
                    case 'show_occupation':
                        $this->menu->platsbankenShowOccupation($chatId, $this->telegram, $previousAction['city_id']);
                        $this->actionHandler->removeLastAction($chatId);
                        break;
                    case 'translate_occupation':
                        $this->menu->platsbankenShowOccupation($chatId, $this->telegram, $previousAction['city_id'], true);
                        $this->actionHandler->removeLastAction($chatId);
                        break;
                    case 'show_specialist':
                        $this->menu->showSpecialist($chatId, $this->telegram, $previousAction['occupation_id'], $previousAction['city_id']);
                        $this->actionHandler->removeLastAction($chatId);
                        break;
                    case 'translate_specialist':
                        $this->menu->transSpec($chatId, $this->telegram, $previousAction['occupation_id'], $previousAction['city_id'], true);
                        $this->actionHandler->removeLastAction($chatId);
                        break;
                    case 'show_p':
                        $this->menu->showResult($chatId, $this->telegram, $previousAction['specialist_id'], $previousAction['city_id'], $startIndex = null);
                        $this->actionHandler->removeLastAction($chatId);
                        break;
                    case 'show_detail_page':
                        $this->menu->showOne($chatId, $this->telegram, $previousAction['detail_id']);
                        $this->actionHandler->removeLastAction($chatId);
                        break;
                    case 'forward_page':
                        $this->menu->showResult($chatId, $this->telegram, $previousAction['specialist_id'], $previousAction['city_id'], $previousAction['page']);
                        $this->actionHandler->removeLastAction($chatId);
                        break;
                    case 'back_page':
                        $this->menu->showResult($chatId, $this->telegram, $previousAction['specialist_id'], $previousAction['city_id'], $previousAction['page']);
                        $this->actionHandler->removeLastAction($chatId);
                        break;
                    default:
                        break;
                }
                break;
            case '/home':
                $this->actionHandler->removeHistoryFile($chatId);
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
        $messageId = $callbackQuery['message']['message_id'];
        $menu = $this->menu;

        if ($this->isJson($callbackData)) {
            $callbackData = json_decode($callbackData, true);
        }


        if (Helper::stringToArray($callbackData)){
            $callbackData = Helper::stringToArray($callbackData);

            $methodName = $callbackData['f'];
            // Вызов метода с передачей параметров
           $callbackData['telegram'] = $this->telegram;
           $callbackData['chat_id'] = $chatId;

            call_user_func([$menu, $methodName], $callbackData);
        }

        switch (true) {

            case array_key_exists('unseen', $callbackData):

                $url = "https://api.telegram.org/bot{$_ENV['TELEGRAM_BOT_TOKEN']}/deleteMessage?chat_id={$chatId}&message_id={$messageId}";

                // Инициализация cURL-сессии
                $ch = curl_init();

                // Установка URL и других нужных параметров
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                // Выполнение запроса, получение ответа и закрытие сессии
                curl_exec($ch);
                curl_close($ch);


                break;

            case array_key_exists('translate', $callbackData):
                $translate_id = $callbackData['translate'];
                $this->actionHandler->addToHistory($chatId, ['type' => 'translate', 'translate_id' => $translate_id]);
                $this->menu->showOneTranslate($chatId, $this->telegram, $translate_id);
                break;
            case array_key_exists('platsbanken', $callbackData):
                $this->actionHandler->addToHistory($chatId, ['type' => 'platsbanken']);
                $menu->showRegion($chatId, $this->telegram);
                break;
            case array_key_exists('show_city', $callbackData):
                $region_id = $callbackData['region_id'];
                $this->actionHandler->addToHistory($chatId, ['type' => 'show_city', 'region_id' => $region_id]);
                $menu->showCity($chatId, $this->telegram, $region_id);
                break;
            case array_key_exists('show_occupation', $callbackData):
                $city_id = $callbackData['city_id'];
                $this->actionHandler->addToHistory($chatId, ['type' => 'show_occupation', 'city_id' => $city_id]);
                $menu->platsbankenShowOccupation($chatId, $this->telegram, $city_id);
                break;
            case array_key_exists('translate_occupation', $callbackData):
                $city_id = $callbackData['translate_occupation'];
                $this->actionHandler->addToHistory($chatId, ['type' => 'translate_occupation', 'city_id' => $city_id]);
                $menu->platsbankenShowOccupation($chatId, $this->telegram, $city_id, true);
                break;
            case array_key_exists('show_specialist', $callbackData):
                $city_id = $callbackData['city_id'];
                $occupation_id = $callbackData['show_specialist'];
                $this->actionHandler->addToHistory($chatId, ['type' => 'show_specialist', 'city_id' => $city_id, 'occupation_id' => $occupation_id]);
                $this->menu->showSpecialist($chatId, $this->telegram, $occupation_id, $city_id);
                break;
            case array_key_exists('translate_specialist', $callbackData):
                $occupation_id = $callbackData['translate_specialist'];
                $city_id = $callbackData['city_id'];
                $this->actionHandler->addToHistory($chatId, ['type' => 'translate_specialist', 'city_id' => $city_id, 'occupation_id' => $occupation_id]);
                $this->menu->transSpec($chatId, $this->telegram, $occupation_id, $city_id, true);
                break;
            case array_key_exists('show_p', $callbackData):
                $specialist_id = $callbackData['show_p'];
                $city_id = $callbackData['city_id'];
                $this->actionHandler->addToHistory($chatId, ['type' => 'show_p', 'city_id' => $city_id, 'specialist_id' => $specialist_id]);
                $this->menu->showResult($chatId, $this->telegram, $specialist_id, $city_id, $startIndex = null);
                break;
            case array_key_exists('show_detail_page', $callbackData):
                $detail_id = $callbackData['detail_id'];
                $this->actionHandler->addToHistory($chatId, ['type' => 'show_detail_page', 'detail_id' => $detail_id]);
                $this->menu->showOne($chatId, $this->telegram, $detail_id);
                break;
            case array_key_exists('forward_page', $callbackData):
                $cityId = $callbackData['ci'];
                $specialist_id = $callbackData['spec'];
                $page = $callbackData['forward_page'];
                $this->actionHandler->addToHistory($chatId, ['type' => 'forward_page', 'city_id' => $cityId, 'specialist_id' => $specialist_id, 'page' => $page]);
                $this->menu->showResult($chatId, $this->telegram, $specialist_id, $cityId, $page);
                break;
            case array_key_exists('back_page', $callbackData):
                $cityId = $callbackData['ci'];
                $specialist_id = $callbackData['spec'];
                $page = $callbackData['back_page'];
                $this->actionHandler->addToHistory($chatId, ['type' => 'back_page', 'city_id' => $cityId, 'specialist_id' => $specialist_id, 'page' => $page]);
                $this->menu->showResult($chatId, $this->telegram, $specialist_id, $cityId, $page);
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
