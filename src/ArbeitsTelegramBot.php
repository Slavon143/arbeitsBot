<?php

namespace src;

use Telegram\Bot\Api;
use Symfony\Component\Dotenv\Dotenv;

class ArbeitsTelegramBot
{
    protected $token;
    protected $incomingRequest;
    protected $telegram;
    protected $parser;

    public function __construct()
    {
        $env = new Dotenv();
        $env->load(__DIR__ . '/../.env');
        $this->token = $_ENV['TELEGRAM_BOT_TOKEN'];

        $this->incomingRequest = json_decode(file_get_contents('php://input'), true);
        $this->telegram = new Api($this->token);

        $this->parser = new Parser();
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
                $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Добро пожаловать!'
                ]);
                $this->startMenu($chatId);
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

        } else {

            switch ($callbackData) {
                case 'platsbanken':
                    $this->navFilter($chatId);
                    break;
                case 'region':
                    $this->navLan($chatId);
                    break;
                default:
                    break;
            }
        }
    }

    public function handleRegionMenu($chatId, $regionKey){
        $region = $this->parser->getCity();

        $cityKeyboard = $this->buildMenuFromId($region,$regionKey);
        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Выберите город:',
            'reply_markup' => json_encode([
                'inline_keyboard' => $cityKeyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ]);
//        file_put_contents(__DIR__ . '/test.txt',var_export($city,1));
    }
    public function buildMenuFromId($data, $parentId) {
        $menu = [];
        foreach ($data as $item) {
            if ($item['id'] === $parentId) {
                foreach ($item['items'] as $subItem) {
                    $menu[] = [
                        [
                            'text' => $subItem['name'],
                            'callback_data' => 'region_key_board ' . $subItem['id'],
                        ]
                    ];
                }
                break; // Найдено значение по id, завершаем цикл
            }
        }
        return $menu;
    }

    public function navLan($chatId)
    {
        $dataСity = $this->parser->getCity();
        $keyboardRegion = $this->buildMenuRegion($dataСity);

        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Выберите регион:',
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboardRegion,
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ]);
    }

    public function buildMenuRegion($items)
    {
        $menu = [];
        $column = [];
        $i = 0;
        foreach ($items as $item) {
            $button = [
                'text' => $item['name'],
                'callback_data' => 'city_key_board ' . $item['id'],
            ];
            $column[] = $button;
            $i++;
            if ($i % 3 === 0) {
                $menu[] = $column;
                $column = [];
            }
        }
        // Добавление оставшихся кнопок, если их количество не делится на 3 без остатка
        if (!empty($column)) {
            $menu[] = $column;
        }
        return $menu;
    }


    public function startMenu($chatId)
    {
        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Выберите действие:',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => 'Platsbanken', 'callback_data' => 'platsbanken'],
                        ['text' => 'Externa webbplatser', 'callback_data' => 'webbplatser']
                    ]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ]);
    }

    public function navFilter($chatId)
    {
        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Выберите фильтр:',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => 'Län', 'callback_data' => 'region']],
                    [['text' => 'Yrkesområden', 'callback_data' => 'yrkesområden']]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ]);
    }
}
