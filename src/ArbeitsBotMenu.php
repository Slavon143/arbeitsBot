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
        // Запрос объявлений с указанным startIndex
        $getAll = $this->apiArbeits->makeApiRequest('https://platsbanken-api.arbetsformedlingen.se/jobs/v1/search', [
            'source' => 'pb',
            'maxRecords' => 25,
            'startIndex' => $startIndex,
        ]);
        $getAll = json_decode($getAll, true);

        // Построение меню из объявлений
        $menu = $this->buildMenuFromAds($getAll);

        // Отправка сообщения с кнопками объявлений
        $objTelegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Выберите объявление:',
            'reply_markup' => json_encode([
                'inline_keyboard' => $menu,
            ]),
        ]);

        // Создание массива для кнопок "Далее" и "Назад"
        $buttons = [];

        // Если startIndex больше 0, добавляем кнопку "Назад"
        if ($startIndex > 0) {
            $buttons[] = [['text' => 'Назад', 'callback_data' => "platsbanken_prev_$startIndex"]];
        }

        // Если startIndex + 25 меньше либо равно offsetLimit, добавляем кнопку "Далее"
        if ($startIndex + 25 <= $getAll['offsetLimit']) {
            $buttons[] = [['text' => 'Далее', 'callback_data' => "platsbanken_next_$startIndex"]];
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

    public function buildMenuFromAds($ads) {

        $menu = [];
        foreach ($ads['ads'] as $ad) {
            $menu[] = [
                [
                    'text' => $ad['title'],
                    'callback_data' => 'ad_key_board ' . $ad['id'],
                ]
            ];
        }
        return $menu;

    }

}