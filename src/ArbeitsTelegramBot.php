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

        $this->menu = new ArbeitsBotMenu();

        $this->actionHandler = new ActionHandler(__DIR__ . '/../db/database.db');
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
            case '/home':
                $this->actionHandler->removeHistoryFile($chatId);
                $this->menu->startMenu($chatId, $this->telegram);
                break;
            case '/back':
                $previousAction = $this->actionHandler->getPreviousAction($chatId);

                $previousAction['telegram'] = $this->telegram;
                $previousAction['chat_id'] = $chatId;
                $previousAction['message_id'] = $message['message_id'];

                call_user_func([$this->menu, $previousAction['f']], $previousAction);
                $this->actionHandler->removeLastAction($chatId);
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

        if (Helper::stringToArray($callbackData)) {
            $callbackData = Helper::stringToArray($callbackData);
            $methodName = $callbackData['f'];

            $this->actionHandler->addToHistory($chatId, $callbackData);
            // Вызов метода с передачей параметров
            $callbackData['telegram'] = $this->telegram;
            $callbackData['chat_id'] = $chatId;
            $callbackData['message_id'] = $callbackQuery['message']['message_id'];

            call_user_func([$menu, $methodName], $callbackData);
        }
    }
}
