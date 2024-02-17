<?php

namespace src;

class ArbeitsBotMenu
{
    public $apiArbeits;
    public $apiTranslate;

    public function __construct()
    {
        $this->apiArbeits = new ApiArbetsformedlingen();
        $this->apiTranslate = new TranslateApi();
    }

    public function startMenu($chatId, $objTelegram)
    {
        $objTelegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Сделайте выбор ресурсов:',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => 'Platsbanken (Банк локаций)', 'callback_data' => Helper::arrayToString(['f'=>'showRegion'])],
                        ['text' => 'Externa webbplatser (Внешние сайты)', 'callback_data' => 'webbplatser'],
                    ]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ]);
    }

    public function showRegion($param)
    {
        $telegram = $param['telegram'];
        $chatId = $param['chat_id'];

        $getLocation = $this->apiArbeits->getLocation();

        $buttons = [];

        // Разбиваем кнопки на две колонки
        $columns = 2;
        $current_column = 0;
        $current_row = [];

        foreach ($getLocation as $item) {
            $id = $item['id'];
            $name = $item['name'];

            // Добавляем кнопку в текущую строку
            $current_row[] = ['text' => $name, 'callback_data' =>  Helper::arrayToString(['f'=>'showCity','r_id'=>$id])];

            // Если текущая строка достигла максимальной ширины, добавляем ее в массив кнопок и создаем новую строку
            $current_column++;
            if ($current_column >= $columns) {
                $buttons[] = $current_row;
                $current_row = [];
                $current_column = 0;
            }
        }

        // Если осталась неполная строка, добавляем ее в массив кнопок
        if (!empty($current_row)) {
            $buttons[] = $current_row;
        }

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Выберите регион:',
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons
            ]),
        ]);
    }

    public function transSpec($param)
    {

        $telegram = $param['telegram'];
        $chatId = $param['chat_id'];
        $occupation_id = $param['ok_id'];
        $city_id = ['c_id'];

        $occupation = $this->apiArbeits->getOccupation();

        $occupationTranslate = Helper::specialistDataTranslate($occupation, $occupation_id, $this->apiTranslate);

        if ($occupationTranslate) {
            $buttons = [];
            $row = [];
            $columns = 2; // Количество колонок

            foreach ($occupationTranslate as $item) {
                $id = $item['id'];
                $name = $item['name'];

                // Добавляем кнопку в текущий ряд
                $row[] = ['text' => $name, 'callback_data' =>  Helper::arrayToString(['f'=>'showResult','spec_id'=>$id,'c_id'=>$city_id])];

                // Если текущий ряд заполнен, добавляем его в массив кнопок и создаем новый ряд
                if (count($row) >= $columns) {
                    $buttons[] = $row;
                    $row = [];
                }
            }

            // Если остались кнопки в текущем ряду, добавляем его в массив кнопок
            if (!empty($row)) {
                $buttons[] = $row;
            }

            // Отправляем сообщение с кнопками
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Выберите направление:',
                'reply_markup' => json_encode([
                    'inline_keyboard' => $buttons
                ]),
            ]);

        }
    }


    public function showSpecialist($param)
    {
        $occupation_id = $param['ok_id'];
        $chatId = $param['chat_id'];
        $telegram = $param['telegram'];
        $city_id = $param['c_id'];

        $occupation = $this->apiArbeits->getOccupation();
        $buttons = [];

        // Указываем количество колонок
        $columns = 2;

        // Инициализируем переменные для текущей колонки и строки
        $current_column = 0;
        $current_row = [];

        foreach ($occupation as $item) {
            if ($item['id'] == $occupation_id) {
                foreach ($item['items'] as $profession) {
                    $id = $profession['id'];
                    $name = $profession['name'];

                    // Добавляем кнопку в текущую строку
                    $current_row[] = ['text' => $name, 'callback_data' => Helper::arrayToString(['f'=>'showResult','spec_id'=>$id,'c_id'=>$city_id])];

                    // Увеличиваем счетчик текущей колонки
                    $current_column++;

                    // Если текущая колонка достигла максимальной ширины, добавляем текущую строку в массив кнопок и создаем новую строку
                    if ($current_column >= $columns) {
                        $buttons[] = $current_row;
                        $current_row = [];
                        $current_column = 0;
                    }
                }
            }
        }

        // Если осталась неполная строка, добавляем ее в массив кнопок
        if (!empty($current_row)) {
            $buttons[] = $current_row;
        }

        // Добавляем кнопку "Перевести" в массив кнопок
        $ukrainian_flag_unicode = "🇺🇦";
        $buttons[] = [[
            'text' => $ukrainian_flag_unicode . ' Перевести:',
            'callback_data' => Helper::arrayToString(['f'=>'transSpec','ok_id'=>$occupation_id,'c_id'=>$city_id])
        ]];

        // Отправляем сообщение с кнопками
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Выберите специальность:',
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons
            ]),
        ]);
    }


    public function showCity($param)
    {

        $telegram = $param['telegram'];
        $region_id = $param['r_id'];
        $chatId = $param['chat_id'];

        $getLocation = $this->apiArbeits->getLocation();
        $buttons = [];

        // Разбиваем кнопки на два ряда
        $columns = 2;
        $current_column = 0;
        $current_row = [];

        foreach ($getLocation as $item) {
            if ($item['id'] == $region_id) {
                foreach ($item['items'] as $city) {
                    $id = $city['id'];
                    $name = $city['name'];

                    // Добавляем кнопку в текущий ряд
                    $current_row[] = ['text' => $name, 'callback_data' =>  Helper::arrayToString(['f'=>'platsbankenShowOccupation','c_id'=>$id])];

                    // Если текущий ряд достиг максимальной ширины, добавляем его в массив кнопок и создаем новый ряд
                    $current_column++;
                    if ($current_column >= $columns) {
                        $buttons[] = $current_row;
                        $current_row = [];
                        $current_column = 0;
                    }
                }
            }
        }

        // Если остался неполный ряд, добавляем его в массив кнопок
        if (!empty($current_row)) {
            $buttons[] = $current_row;
        }

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Выберите город:',
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons
            ]),
        ]);
    }


    public function platsbankenShowOccupation($param)
    {
        $telegram = $param['telegram'];
        $city_id = $param['c_id'];
        $chatId = $param['chat_id'];
        $translate = $param['trans'];

        $occupation = $this->apiArbeits->getOccupation();
        if ($translate) {
            $translateApi = new TranslateApi();
            $occupation = Helper::occupationDataTranslate($occupation, $translateApi);
        }
        $buttons = [];

        // Разбиваем кнопки на два ряда
        $columns = 2;
        $current_column = 0;
        $current_row = [];

        foreach ($occupation as $item) {
            $id = $item['id'];
            $name = $item['name'];

            // Добавляем кнопку в текущий ряд
            $current_row[] = ['text' => $name, 'callback_data' => Helper::arrayToString(['f'=>'showSpecialist','ok_id'=>$id,'c_id'=>$city_id])];

            // Если текущий ряд достиг максимальной ширины, добавляем его в массив кнопок и создаем новый ряд
            $current_column++;
            if ($current_column >= $columns) {
                $buttons[] = $current_row;
                $current_row = [];
                $current_column = 0;
            }
        }

        // Если остался неполный ряд, добавляем его в массив кнопок
        if (!empty($current_row)) {
            $buttons[] = $current_row;
        }
        if (!$translate) {
            // Добавляем кнопку "Перевести" в массив кнопок
            $ukrainian_flag_unicode = "🇺🇦"; // Unicode символ для украинского флага
            $buttons[] = [[
                'text' => $ukrainian_flag_unicode . ' Перевести:',
                'callback_data' =>  Helper::arrayToString(['f'=>'platsbankenShowOccupation','c_id'=>$city_id,'trans'=>true])

            ]];
        }
        // Отправляем сообщение с кнопками
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Выберите направление:',
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons
            ]),
        ]);
    }


    public function showResult($param)
    {

        $telegram = $param['telegram'];
        $chatId = $param['chat_id'];
        $city_id = $param['c_id'];
        $specialist_id = $param['spec_id'];

        if (isset($param['st_index'])){
            $startIndex = $param['st_index'];
        }else{
            $startIndex = 0;
        }
        $getAll = $this->apiArbeits->showAll($startIndex, $city_id, $specialist_id);

        $numberOfAds = $getAll['numberOfAds'];

        if ($numberOfAds == 0) {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'К сожалению, объявлений не найдено.'
            ]);
            return;
        }

        $this->buildMenuFromAds($getAll, $chatId, $telegram);

        // Рассчитываем общее количество страниц
        $totalPages = ceil($numberOfAds / 5);

        // Если всего одна страница, не добавляем кнопки навигации вперед/назад
        if ($totalPages == 1) {
            $inlineKeyboard = [];
        } else {
            // Создаем кнопки
            $inlineKeyboard = [];

            $left_button = ['text' => '←', 'callback_data' => Helper::arrayToString(['f'=>'showResult','st_index'=>$startIndex -5,'spec_id'=>$specialist_id,'c_id'=>$city_id])];
            $right_button = ['text' => '→', 'callback_data' => Helper::arrayToString(['f'=>'showResult','st_index'=>$startIndex +5,'spec_id'=>$specialist_id,'c_id'=>$city_id])];

            // Проверяем, нужно ли показывать кнопку "Назад"
            if ($startIndex > 0) {
                $inlineKeyboard[] = $left_button;
            }

            // Добавляем кнопку со страницами
            $currentPage = $startIndex / 5 + 1; // Рассчитываем номер текущей страницы
            $page_button = ['text' => $currentPage . '/' . $totalPages, 'callback_data' => 'None'];
            $inlineKeyboard[] = $page_button;

            // Проверяем, нужно ли показывать кнопку "Вперед"
            if ($startIndex + 5 < $numberOfAds) {
                $inlineKeyboard[] = $right_button;
            }
            // Отправляем сообщение с клавиатурой
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Выберите действие:',
                'reply_markup' => json_encode(['inline_keyboard' => [$inlineKeyboard]])
            ]);
        }
    }


    public function buildMenuFromAds($ads, $chatId, $objTelegram)
    {
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

            // Формируем кнопку "Подробнее" и кнопку "Скрыть" для каждого объявления
            $menu = [
                [
                    'text' => '⏬ Подробнее',
                    'callback_data' => Helper::arrayToString(['f'=>'showOne','detail_id'=>$ad['id']]),
                ],
                [
                    'text' => 'Скрыть',
                    'callback_data' => json_encode(['unseen' => '']),
                ]
            ];

            // Отправляем сообщение с полной информацией об объявлении и кнопкой "Подробнее" и "Скрыть"
            $objTelegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $messageText,
                'reply_markup' => json_encode(['inline_keyboard' => [$menu]]),
                'parse_mode' => 'HTML',
            ]);
        }
    }


    public function showOneTranslate($chatId, $telegram, $key_board)
    {
        $ad = $this->apiArbeits->getOne($key_board);

        //newArray
        require __DIR__ . '/../settings/ArraySettings.php';

        $translate = Helper::processJobData($ad, $newArray);

        $translate = $this->apiTranslate->translate($translate);

        if (!empty($translate)) {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => strip_tags($translate),
                'parse_mode' => 'HTML', // Это для того, чтобы текст интерпретировался как HTML
            ]);
        }
    }

    public function showOne($param)
    {
        $telegram = $param['telegram'];
        $chatId = $param['chat_id'];
        $key_board = $param['detail_id'];

        $ad = $this->apiArbeits->getOne($key_board);

        //newArray
        require __DIR__ . '/../settings/ArraySettings.php';

        $str = Helper::processJobData($ad, $newArrayUa);

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $str,
            'parse_mode' => 'HTML', // Это для того, чтобы текст интерпретировался как HTML
        ]);

        $ukrainian_flag_unicode = "🇺🇦"; // Unicode символ для украинского флага

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $ukrainian_flag_unicode . ' Перевести:',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => $ukrainian_flag_unicode . ' Перевести:', 'callback_data' => Helper::arrayToString(['f'=>'showOneTranslate','detail_id'=>$key_board])]
                    ]
                ]
            ]),
        ]);
    }
}