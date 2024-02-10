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

    public function startMenu($chatId, $objTelegram){
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

    public function platsbankenMenu($chatId, $objTelegram){
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

    public function platsbankenShowAll($chatId, $objTelegram, $startIndex = null) {

        $getAll = $this->apiArbeits->showAll($startIndex);
        // Построение меню из объявлений
         $this->buildMenuFromAds($getAll,$chatId,$objTelegram);

        // Создание массива для кнопок "Далее" и "Назад"
        $buttons = [];

        // Если startIndex больше 0, добавляем кнопку "Назад"
        if ($startIndex > 0) {
            $buttons[] = [['text' => '«Назад', 'callback_data' => "platsbanken_prev_$startIndex"]];
        }

        // Если startIndex + 5 меньше либо равно offsetLimit, добавляем кнопку "Далее"
        if ($startIndex + 5 <= $getAll['offsetLimit']) {
            $buttons[] = [['text' => 'Далее»', 'callback_data' => "platsbanken_next_$startIndex"]];
        }

        // Отправка сообщения с кнопками "Далее" и "Назад"
        if (!empty($buttons)) {
            $objTelegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Выберите действие:',
                'reply_markup' => json_encode([
                    'inline_keyboard' => $buttons,
                ]),
            ]);
        }
    }

    public function buildMenuFromAds($ads,$chatId,$objTelegram) {
        $menu = [];
        foreach ($ads['ads'] as $ad) {
            // Создаем текст кнопки с информацией об объявлении

            $title = $ad['title'];
            $publishedDate = $ad['publishedDate'];
            $occupation = $ad['occupation'];
            $workplace = $ad['workplace'];
            $workplaceName = $ad['workplaceName'];
            $positions = $ad['positions'];

            // Создаем текст кнопки с заголовком объявления
            $buttonText = $title;

            // Создаем текст с остальной информацией для вывода под кнопкой
            $additionalInfo =
                "<b>Дата публикации:</b> " . $publishedDate . "\n" .
                "<b>Профессия:</b> " . $occupation . "\n" .
                "<b>Место работы:</b> " . $workplace . "\n" .
                "<b>Название места работы:</b> " . $workplaceName . "\n" .
                "<b>Количество позиций:</b> " . $positions;

            // Отправляем сообщение с полной информацией о каждом объявлении под кнопкой
            $objTelegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "<b>$title</b>\n$additionalInfo",
                'parse_mode' => 'HTML', // Это для того, чтобы текст интерпретировался как HTML
            ]);
            $objTelegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Подробнее',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => '⬇️', 'callback_data' => 'ad_key_board ' . $ad['id']],
                        ]
                    ],
                ]),
            ]);


//            $buttonText =
//                "Заголовок: " . $ad['title'] . "\n" .
//                "Дата публикации: " . $ad['publishedDate'] . "\n" .
//                "Профессия: " . $ad['occupation'] . "\n" .
//                "Место работы: " . $ad['workplace'] . "\n" .
//                "Название места работы: " . $ad['workplaceName'] . "\n" .
//                "Количество позиций: " . $ad['positions'];
//
//            // Создаем кнопку с текстом объявления
//            $menu[] = [
//                [
//                    'text' => $buttonText,
//                    'callback_data' => 'ad_key_board ' . $ad['id'],
//                ]
//            ];
        }
//        return $menu;
    }




}