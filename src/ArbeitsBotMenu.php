<?php

namespace src;

use src\Parser;

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
            'text' => 'Сделайте выбор ресурсов:',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => 'Platsbanken (Банк локаций)', 'callback_data' => json_encode(['platsbanken' => ''])],
                        ['text' => 'Externa webbplatser (Внешние сайты)', 'callback_data' => 'webbplatser']
                    ]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ]);
    }

    public function showRegion($chatId, $telegram)
    {
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
            $current_row[] = ['text' => $name, 'callback_data' => json_encode(['show_city' => '', 'region_id' => $id])];

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


    public function platsbankenShowOccupationClass($chatId, $telegram, $occupation_id, $city_id)
    {
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
                    $current_row[] = ['text' => $name, 'callback_data' => json_encode(['show_profession' => $id, 'city_id' => $city_id])];

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

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Выберите специальность:',
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons
            ]),
        ]);
    }


    public function showCity($chatId, $telegram, $region_id)
    {
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
                    $current_row[] = ['text' => $name, 'callback_data' => json_encode(['show_occupation' => '', 'city_id' => $id])];

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


    public function platsbankenShowOccupation($chatId, $telegram, $city_id)
    {
        $occupation = $this->apiArbeits->getOccupation();
        $buttons = [];

        // Разбиваем кнопки на два ряда
        $columns = 2;
        $current_column = 0;
        $current_row = [];

        foreach ($occupation as $item) {
            $id = $item['id'];
            $name = $item['name'];

            // Добавляем кнопку в текущий ряд
            $current_row[] = ['text' => $name, 'callback_data' => json_encode(['show_specialist' => $id, 'city_id' => $city_id])];

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

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Выберите направление:',
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons
            ]),
        ]);
    }


    public function showResult($chatId, $telegram, $specialist_id, $city_id, $startIndex = null)
    {
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

        $inlineKeyboard = [];

        // Добавляем информацию о текущей странице и общем количестве объявлений
        $currentPage = $startIndex / 5 + 1; // Рассчитываем номер текущей страницы
        $totalPages = ceil($numberOfAds / 5); // Рассчитываем общее количество страниц

        // Создаем кнопки
        $left_button = ['text' => '←', 'callback_data' => json_encode(['back_page' => $startIndex - 5, 'ci' => $city_id, 'spec' => $specialist_id])];
        $right_button = ['text' => '→', 'callback_data' => json_encode(['forward_page' => $startIndex + 5, 'ci' => $city_id, 'spec' => $specialist_id])];

        // Проверяем, нужно ли показывать кнопку "Назад"
        if ($startIndex > 0) {
            $inlineKeyboard[] = $left_button;
        }

        // Добавляем кнопку со страницами
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
                    'callback_data' => json_encode(['show_detail_page' => '', 'detail_id' => $ad['id']]),
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

    public function truncateText($text, $length = 1000)
    {
        // Обрезаем текст до заданной длины
        $truncatedText = mb_substr($text, 0, $length);
        // Удаляем последнее слово, чтобы избежать обрыва слова
        $truncatedText = preg_replace('/\s+?(\S+)?$/', '', $truncatedText);
        return $truncatedText;
    }

    public function showOne($chatId, $telegram, $key_board)
    {
        $ad = $this->apiArbeits->getOne($key_board);

        //newArray
        require __DIR__ . '/../settings/ArraySettings.php';

        $flattenedArray = $this->flattenArray($ad);
        $flattenedArray['description'] = $this->truncateText(strip_tags(str_ireplace("\n", '', $flattenedArray['description'])));
        $rename = $this->renameKeys($flattenedArray, $newArray);

        $str = '';
        foreach ($rename as $key => $item) {
            $str .= '<b>' . $key . '</b>' . ': ' . $item . "\n";
        }

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $str,
            'parse_mode' => 'HTML', // Это для того, чтобы текст интерпретировался как HTML
        ]);
    }

    public function renameKeys($array, $renameArray)
    {
        $result = [];
        foreach ($renameArray as $oldKey => $newKey) {
            if (isset($array[$oldKey])) {
                $result[$newKey] = $array[$oldKey];
            }
        }
        return $result;
    }

    public function flattenArray($array, $prefix = '')
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $prefix . $key . '_'));
            } elseif (!empty($value)) {
                $result[$prefix . $key] = $value;
            }
        }
        return $result;
    }

    public function debug($data)
    {
        file_put_contents(__DIR__ . '/classDebug.txt', var_export($data, 1));
    }
}