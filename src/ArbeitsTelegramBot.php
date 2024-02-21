<?php

namespace src;

use src\ActionHandler;

use Telegram\Bot\Api;
use Symfony\Component\Dotenv\Dotenv;


class ArbeitsTelegramBot
{
    protected $token;
    protected $update;
    protected $telegram;
    protected $chat_id;
    protected $menu;

    protected $actionHandler;

    public function __construct()
    {
        $env = new Dotenv();
        $env->load(__DIR__ . '/../.env');
        $this->token = $_ENV['TELEGRAM_BOT_TOKEN'];
        $this->update = json_decode(file_get_contents('php://input'), true);
        $this->telegram = new Api($this->token);
        $this->chat_id = $this->extractChatId($this->update);
        $this->actionHandler = new ActionHandler(__DIR__ . '/../db/database.db');
        $this->menu = new ArbeitsBotMenu($this->chat_id, $this->telegram, $this->actionHandler);
    }

    public function listen()
    {
        $message = $this->update['message'] ?? null;
        $callbackQuery = $this->update['callback_query'] ?? null;

        if (!$this->actionHandler->getLanguageChoices($this->chat_id)) {
            if ($callbackQuery) {

                $selectedLanguage = Helper::stringToArray($callbackQuery['data']);
                $this->actionHandler->recordLanguageChoice($this->chat_id, $selectedLanguage['lang']);
                $lang = $this->actionHandler->getLanguageChoices($this->chat_id);
                $this->menu->startMenu($lang);
            } else {
                $this->menu->sendLanguageMenu();
            }
        } else {
            if ($callbackQuery) {
                if (isset(Helper::stringToArray($callbackQuery['data'])['lang'])) {
                    $this->actionHandler->recordLanguageChoice($this->chat_id, Helper::stringToArray($callbackQuery['data'])['lang']);
                    $this->menu->startMenu(Helper::stringToArray($callbackQuery['data'])['lang']);
                }
                $this->handleCallbackQuery($callbackQuery);
            } else {
                $this->handleMessage($message);
            }
        }
    }

    protected function handleMessage($message)
    {
        $messageText = $message['text'];
        switch ($messageText) {
            case '/start':
                break;
            case '/changelanguage':
                $this->menu->sendLanguageMenu();
                break;
            case '/home':
                $this->actionHandler->removeHistoryFile($this->chat_id);
                $this->menu->startMenu(false);
                break;
            case '/back':
                $previousAction = $this->actionHandler->getPreviousAction($this->chat_id);
                $previousAction['telegram'] = $this->telegram;
                $previousAction['chat_id'] = $this->chat_id;
                $previousAction['message_id'] = $message['message_id'];

                call_user_func([$this->menu, $previousAction['f']], $previousAction);
                $this->actionHandler->removeLastAction($this->chat_id);
                break;
            default:
                $this->menu->showResult(['se_t' => $message['text']]);
                break;
        }
    }

    protected function handleCallbackQuery($callbackQuery)
    {
        $callbackData = $callbackQuery['data'];
        $menu = $this->menu;

        if (Helper::stringToArray($callbackData)) {
            $callbackData = Helper::stringToArray($callbackData);
            $methodName = $callbackData['f'];

            $this->actionHandler->addToHistory($this->chat_id, $callbackData);
            // Вызов метода с передачей параметров

            $callbackData['message_id'] = $callbackQuery['message']['message_id'];
            call_user_func([$menu, $methodName], $callbackData);
        }
    }

    private function extractChatId($update)
    {
        if ($update && isset($update['message']['chat']['id'])) {
            return $update['message']['chat']['id'];
        } elseif ($update && isset($update['callback_query']['message']['chat']['id'])) {
            return $update['callback_query']['message']['chat']['id'];
        }

        // Возвращаем значение по умолчанию (может потребоваться в вашем случае)
        return null;
    }
}
