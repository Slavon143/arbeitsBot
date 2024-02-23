<?php

namespace src;

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
                $this->menu->startMenu($selectedLanguage['lang']);
            } else {
                $this->menu->sendLanguageMenu();
            }
        } else {
            if ($callbackQuery) {
                if (isset(Helper::stringToArray($callbackQuery['data'])['lang'])) {
                    $this->actionHandler->recordLanguageChoice($this->chat_id, Helper::stringToArray($callbackQuery['data'])['lang']);
                    $selectedLanguage = Helper::stringToArray($callbackQuery['data']);
                    $this->menu->startMenu($selectedLanguage['lang']);
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

        // Проверяем, начинается ли строка с /suggest
        if (strpos($messageText, '/suggest') === 0) {
            $suggestionText = str_replace('/suggest', '', $messageText);
            $suggestionText = trim($suggestionText);
            $this->menu->sendMeMessage($suggestionText);
        } else {
            // В случае отсутствия /suggest, выполняем switch
            switch ($messageText) {
                case '/start':
                    $this->actionHandler->removeHistoryFile($this->chat_id);
                    $this->menu->startMenu('ru');
                    break;
                case '🌐 Language':
                    $this->menu->sendLanguageMenu();
                    break;
                case '🏠 Home':
                    $this->actionHandler->removeHistoryFile($this->chat_id);
                    $this->menu->startMenu(false);
                    break;
                case '🔙 Back':
                    $previousAction = $this->actionHandler->getPreviousAction($this->chat_id);
                    $previousAction['message_id'] = $message['message_id'];
                    call_user_func([$this->menu, $previousAction['f']], $previousAction);
                    $this->actionHandler->removeLastAction($this->chat_id);
                    break;
                case '/help':
                    $this->menu->menuHelp();
                    break;
                default:
                    $this->menu->showResult(['se_t' => $message['text']]);
                    break;
            }
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
        return null;
    }
}
