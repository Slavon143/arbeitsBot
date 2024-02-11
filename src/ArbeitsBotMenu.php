<?php

namespace src;

use src\Parser;
use src\ApiArbetsformedlingen;

class ArbeitsBotMenu
{

    public $apiArbeits;


    public function __construct()
    {
        $this->apiArbeits = new ApiArbetsformedlingen();
    }

    public function startMenu($chatId, $objTelegram)
    {
        $objTelegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Сделайте выбор:',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => 'Platsbanken (Банк локаций)', 'callback_data' => 'platsbanken'],
                        ['text' => 'Externa webbplatser (Внешние сайты)', 'callback_data' => 'webbplatser']
                    ]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ]);
    }

    public function platsbankenMenu($chatId, $objTelegram)
    {
        $objTelegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Сделайте выбор:',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => 'Показать все', 'callback_data' => 'platsbanken_show_all'],
                        ['text' => 'Фильтр', 'callback_data' => 'platsbanken_filter']
                    ]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ]);
    }

    public function showRegion($chatId, $telegram)
    {
        // Получение данных для кнопок
        $getLocation = $this->apiArbeits->getLocation();

// Создание массива кнопок
        $buttons = [];

        foreach ($getLocation as $item) {
            $id = $item['id'];
            $name = $item['name'];
            $buttons[] = [
                ['text' => $name,
                    'callback_data' => 'filter_region_id_' . $id]
            ];
        }

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Выберите ргион:',
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons
            ]),
        ]);
    }

    public function showCity($chatId,$telegram,$region_id){
        $getLocation = $this->apiArbeits->getLocation();

        $buttons = [];

        foreach ($getLocation as $item){
            if ($item['id'] == $region_id){
                foreach ($item['items'] as $city){
                    $id = $city['id'];
                    $name = $city['name'];
                    $buttons[] = [
                        ['text' => $name,
                            'callback_data' => 'filter_city_id_' . $id]
                    ];
                }
            }
        }
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Выберите город:',
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons
            ]),
        ]);

    }

    public function platsbankenFilter($chatId,$telegram){
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Сделайте выбор:',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => 'Ort (Место)', 'callback_data' => 'platsbanken_filter_ort'],
                        ['text' => 'Yrke (Профессия)', 'callback_data' => 'platsbanken_filter_yrke']
                    ]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ]);
    }

    public function platsbankenShowAll($chatId, $objTelegram, $startIndex = null)
    {

        $getAll = $this->apiArbeits->showAll($startIndex);
        // Построение меню из объявлений
        $this->buildMenuFromAds($getAll, $chatId, $objTelegram);

        // Создание массива для кнопок "Далее" и "Назад"
        $buttons = [
            [
                ['text' => '', 'callback_data' => 'platsbanken_filter_ort'],
                ['text' => '', 'callback_data' => 'platsbanken_filter_yrke']
            ]
        ];

        // Если startIndex больше 0, добавляем кнопку "Назад"
        if ($startIndex > 0) {
            $buttons[0][0]['text'] = '⬅️ Назад';
            $buttons[0][0]['callback_data'] = "platsbanken_prev_$startIndex";
        }

        // Если startIndex + 5 меньше либо равно offsetLimit, добавляем кнопку "Далее"
        if ($startIndex + 5 <= $getAll['offsetLimit']) {
            $buttons[0][1]['text'] = 'Далее ➡️';
            $buttons[0][1]['callback_data'] = "platsbanken_next_$startIndex";
        }

        // Отправка сообщения с кнопками "Далее" и "Назад"
        if (!empty($buttons)) {
            file_put_contents(__DIR__ . '/rrr.txt',var_export($buttons,1));
            $objTelegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Выберите действие:',
                'reply_markup' => json_encode([
                    'inline_keyboard' => $buttons,
                ]),
            ]);
        }
    }

    public function buildMenuFromAds($ads, $chatId, $objTelegram)
    {
        $messages = []; // Массив для хранения текста сообщений с полной информацией

        foreach ($ads['ads'] as $ad) {
            // Создаем текст сообщения с полной информацией об объявлении
            $title = $ad['title'];
            $publishedDate = $ad['publishedDate'];
            $occupation = $ad['occupation'];
            $workplace = $ad['workplace'];
            $workplaceName = $ad['workplaceName'];
            $positions = $ad['positions'];

            $additionalInfo =
                "<b>Дата публикации:</b> " . $publishedDate . "\n" .
                "<b>Профессия:</b> " . $occupation . "\n" .
                "<b>Место работы:</b> " . $workplace . "\n" .
                "<b>Название места работы:</b> " . $workplaceName . "\n" .
                "<b>Количество позиций:</b> " . $positions;

            // Создаем текст сообщения
            $messageText = "<b>$title</b>\n$additionalInfo";

            // Формируем кнопку "Подробнее" для каждого объявления
            $menu = [
                [
                    'text' => '⏬ Подробнее',
                    'callback_data' => 'ad_key_board_' . $ad['id'],
                ]
            ];

            // Добавляем текст сообщения и кнопку в массив сообщений
            $messages[] = [
                'text' => $messageText,
                'reply_markup' => json_encode(['inline_keyboard' => [$menu]]),
                'parse_mode' => 'HTML',
            ];
        }

        // Отправляем сообщения с полной информацией об объявлениях
        foreach ($messages as $message) {
            $objTelegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message['text'],
                'reply_markup' => $message['reply_markup'],
                'parse_mode' => $message['parse_mode'],
            ]);
        }
    }

    public function showOne($chatId, $telegram, $key_board)
    {

        $ad = $this->apiArbeits->getOne($key_board);
        $title = isset($ad['title']) && !empty($ad['title']) ? strip_tags($ad['title']) : '';
        $description = isset($ad['description']) && !empty($ad['description']) ? strip_tags($ad['description']) : '';
        $publishedDate = isset($ad['publishedDate']) && !empty($ad['publishedDate']) ? strip_tags($ad['publishedDate']) : '';
        $occupation = isset($ad['occupation']) && !empty($ad['occupation']) ? strip_tags($ad['occupation']) : '';
        $companyName = isset($ad['company']['name']) && !empty($ad['company']['name']) ? strip_tags($ad['company']['name']) : '';
        $webAddress = isset($ad['company']['webAddress']) && !empty($ad['company']['webAddress']) ? strip_tags($ad['company']['webAddress']) : '';
        $phoneNumber = isset($ad['company']['phoneNumber']) && !empty($ad['company']['phoneNumber']) ? strip_tags($ad['company']['phoneNumber']) : '';
        $email = isset($ad['company']['email']) && !empty($ad['company']['email']) ? strip_tags($ad['company']['email']) : '';
        $organisationNumber = isset($ad['company']['organisationNumber']) && !empty($ad['company']['organisationNumber']) ? strip_tags($ad['company']['organisationNumber']) : '';
        $region = isset($ad['workplace']['region']) && !empty($ad['workplace']['region']) ? strip_tags($ad['workplace']['region']) : '';
        $municipality = isset($ad['workplace']['municipality']) && !empty($ad['workplace']['municipality']) ? strip_tags($ad['workplace']['municipality']) : '';

        $additionalInfo =
            (!empty($occupation) ? "<b>Профессия:</b> " . $occupation . "\n" : '') .
            (!empty($description) ? "<b>Описание:</b> " . $description . "\n" : '') .
            (!empty($companyName) ? "<b>Название компании:</b> " . $companyName . "\n" : '') .
            (!empty($webAddress) ? "<b>Веб-адрес компании:</b> " . $webAddress . "\n" : '') .
            (!empty($phoneNumber) ? "<b>Телефон компании:</b> " . $phoneNumber . "\n" : '') .
            (!empty($email) ? "<b>Email компании:</b> " . $email . "\n" : '') .
            (!empty($organisationNumber) ? "<b>Организационный номер компании:</b> " . $organisationNumber . "\n" : '') .
            (!empty($region) ? "<b>Регион:</b> " . $region . "\n" : '') .
            (!empty($municipality) ? "<b>Муниципалитет:</b> " . $municipality . "\n" : '') .
            (!empty($publishedDate) ? "<b>Дата публикации:</b> " . $publishedDate . "\n" : '');

        // Отправляем сообщение с основной информацией о каждом объявлении
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "<b>$title</b>\n$additionalInfo",
            'parse_mode' => 'HTML', // Это для того, чтобы текст интерпретировался как HTML
        ]);
    }
}