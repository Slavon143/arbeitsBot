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

        foreach ($getLocation as $item) {
            $id = $item['id'];
            $name = $item['name'];
            $buttons[] = [
                ['text' => $name,
                    'callback_data' => json_encode(['show_city' => '', 'region_id' => $id])]
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

    public function platsbankenShowOccupationClass($chatId, $telegram, $occupation_id, $city_id)
    {
        $occupation = $this->apiArbeits->getOccupation();
        $buttons = [];
        foreach ($occupation as $item) {
            if ($item['id'] == $occupation_id) {
                foreach ($item['items'] as $profession) {
                    $id = $profession['id'];
                    $name = $profession['name'];
                    $buttons[] = [
                        ['text' => $name,
                            'callback_data' => json_encode(['show_profession' => $id, 'city_id' => $city_id])]
                    ];
                }
            }
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
        foreach ($getLocation as $item) {
            if ($item['id'] == $region_id) {
                foreach ($item['items'] as $city) {
                    $id = $city['id'];
                    $name = $city['name'];
                    $buttons[] = [
                        ['text' => $name,
                            'callback_data' => json_encode(['show_occupation' => '', 'city_id' => $id])]
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

    public function platsbankenShowOccupation($chatId, $telegram, $city_id)
    {
        $occupation = $this->apiArbeits->getOccupation();

        $buttons = [];

        foreach ($occupation as $item) {
            $id = $item['id'];
            $name = $item['name'];
            $buttons[] = [
                ['text' => $name,
                    'callback_data' => json_encode(['show_specialist' => $id, 'city_id' => $city_id])]
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

        // Добавляем кнопку "Назад"
        if ($startIndex > 0) {
            $inlineKeyboard[] = [['text' => '<< Назад (' . $currentPage . '/' . $totalPages . ')', 'callback_data' => json_encode(['back_page' => $startIndex - 5, 'ci' => $city_id, 'spec' => $specialist_id])]];
        }

        // Добавляем кнопку "Вперед"
        $endIndex = $this->calculatePageCount($numberOfAds, 5);
        if ($endIndex > $startIndex && $numberOfAds > $startIndex + 5) {
            $inlineKeyboard[] = [['text' => 'Вперед >> (' . $currentPage . '/' . $totalPages . ')', 'callback_data' => json_encode(['forward_page' => $startIndex + 5, 'ci' => $city_id, 'spec' => $specialist_id])]];
        }

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => 'Выберите действие:',
            'reply_markup' => json_encode(['inline_keyboard' => $inlineKeyboard])
        ]);
    }


    public function calculatePageCount($totalRecords, $recordsPerPage)
    {
        return ceil($totalRecords / $recordsPerPage) * 5;
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

    public function truncateText($text, $length = 2000)
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

        // Информация о контакте
        $contactsInfo = isset($ad['contacts'][0]) ? $ad['contacts'][0] : [];
        $contactName = isset($contactsInfo['description']) ? strip_tags($contactsInfo['description']) : '';
        $contactEmail = isset($contactsInfo['email']) ? strip_tags($contactsInfo['email']) : '';
        $contactPhoneNumber = isset($contactsInfo['phoneNumber']) ? strip_tags($contactsInfo['phoneNumber']) : '';

        // Информация о заявке
        $applicationInfo = isset($ad['application']) ? $ad['application'] : [];
        $webAddressApplication = isset($applicationInfo['webAddress']) ? $applicationInfo['webAddress'] : '';

        $additionalInfo =
            (!empty($occupation) ? "<b>Профессия:</b> " . $occupation . "\n" : '') .
            (!empty($description) ? "<b>Описание:</b> " . $this->truncateText($description) . "\n" : '') .
            (!empty($companyName) ? "<b>Название компании:</b> " . $companyName . "\n" : '') .
            (!empty($webAddress) ? "<b>Веб-адрес компании:</b> " . $webAddress . "\n" : '') .
            (!empty($phoneNumber) ? "<b>Телефон компании:</b> " . $phoneNumber . "\n" : '') .
            (!empty($email) ? "<b>Email компании:</b> " . $email . "\n" : '') .
            (!empty($organisationNumber) ? "<b>Организационный номер компании:</b> " . $organisationNumber . "\n" : '') .
            (!empty($region) ? "<b>Регион:</b> " . $region . "\n" : '') .
            (!empty($municipality) ? "<b>Муниципалитет:</b> " . $municipality . "\n" : '') .
            (!empty($publishedDate) ? "<b>Дата публикации:</b> " . $publishedDate . "\n" : '') .
            (!empty($contactName) ? "<b>Контактное лицо:</b> " . $contactName . "\n" : '') .
            (!empty($contactEmail) ? "<b>Email контактного лица:</b> " . $contactEmail . "\n" : '') .
            (!empty($contactPhoneNumber) ? "<b>Телефон контактного лица:</b> " . $contactPhoneNumber . "\n" : '') .
            (!empty($webAddressApplication) ? "<b>Ссылка на заявку:</b> " . $webAddressApplication . "\n" : '');

        // Отправляем сообщение с основной информацией о каждом объявлении
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "<b>$title</b>\n$additionalInfo",
            'parse_mode' => 'HTML', // Это для того, чтобы текст интерпретировался как HTML
        ]);
    }

    public function debug($data)
    {
        file_put_contents(__DIR__ . '/classDebug.txt', var_export($data, 1));
    }
}