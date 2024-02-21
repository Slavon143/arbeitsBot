<?php

namespace src;

class ArbeitsBotMenu
{
    public $apiArbeits;
    public $apiTranslate;

    public $language;
    public $telegram;
    public $db;
    public $chat_id;
    public $settingArray;

    public function __construct($chat_id, $telegram, $db)
    {
        $this->apiArbeits = new ApiArbetsformedlingen();
        $this->apiTranslate = new TranslateApi();
        $this->settingArray = new SettingsClass();
        $this->db = $db;
        $this->telegram = $telegram;
        $this->chat_id = $chat_id;
        $this->language = $this->db->getLanguageChoices($this->chat_id);
    }

    public function startMenu($lang = false)
    {
        if ($lang){
            $tramslateText = $this->settingArray->arrSettingStartMenu[$lang];
        }else{
            $tramslateText = $this->settingArray->arrSettingStartMenu[$this->language];
        }
        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => $tramslateText['title'],
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => $tramslateText['platsbankenButton'], 'callback_data' => Helper::arrayToString(['f' => 'showRegion'])],
                        ['text' => $tramslateText['webbplatserButton'], 'callback_data' => 'webbplatser'],
                    ]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ]);
    }


    public function showRegion()
    {

        $tramslateText = $this->settingArray->arrSettingStartMenuRegion[$this->language];

        $getLocation = $this->apiArbeits->getLocation();

        $buttons = [];

        $columns = 2;
        $current_column = 0;
        $current_row = [];

        foreach ($getLocation as $item) {
            $id = $item['id'];
            $name = $item['name'];

            $current_row[] = ['text' => $name, 'callback_data' => Helper::arrayToString(['f' => 'showCity', 'r_id' => $id])];

            $current_column++;
            if ($current_column >= $columns) {
                $buttons[] = $current_row;
                $current_row = [];
                $current_column = 0;
            }
        }

        if (!empty($current_row)) {
            $buttons[] = $current_row;
        }

        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => $tramslateText['title'],
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons
            ]),
        ]);
    }

    public function showSpecialist($param)
    {
        $occupation_id = $param['ok_id'];
        $city_id = $param['c_id'];

        $tramslateText = $this->settingArray->arrSettingStartMenuSpecialist[$this->language];

        $occupation = $this->apiArbeits->getOccupation();

        $buttons = [];
        $columns = 2;
        $current_column = 0;
        $current_row = [];

        if ($param['trans']) {
            $occupation = Helper::translateData($occupation, $this->apiTranslate, $param['trans'], $occupation_id);
            foreach ($occupation as $item) {
                $id = $item['id'];
                $name = $item['name'];

                // Добавляем кнопку в текущий ряд
                $row[] = ['text' => $name, 'callback_data' => Helper::arrayToString(['f' => 'showResult', 'spec_id' => $id, 'c_id' => $city_id])];

                // Если текущий ряд заполнен, добавляем его в массив кнопок и создаем новый ряд
                if (count($row) >= $columns) {
                    $buttons[] = $row;
                    $row = [];
                }
            }
        } else {
            foreach ($occupation as $item) {
                if ($item['id'] == $occupation_id) {
                    foreach ($item['items'] as $profession) {
                        $id = $profession['id'];
                        $name = $profession['name'];

                        // Добавляем кнопку в текущую строку
                        $current_row[] = ['text' => $name, 'callback_data' => Helper::arrayToString(['f' => 'showResult', 'spec_id' => $id, 'c_id' => $city_id])];

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
            if ($this->language == 'uk') {
                $flag_unicode = "🇺🇦";
                $langParam = 'uk';
            } elseif ($this->language == 'ru') {
                $flag_unicode = "🇷🇺";
                $langParam = 'ru';
            } else {
                $flag_unicode = "🇬🇧";
                $langParam = 'en';
            }
            $buttons[] = [[
                'text' => $flag_unicode . ' ' . $tramslateText['buttonTranslate'],
                'callback_data' => Helper::arrayToString(['f' => 'showSpecialist', 'ok_id' => $occupation_id, 'c_id' => $city_id, 'trans' => $langParam])
            ]];
        }
        if (!empty($current_row)) {
            $buttons[] = $current_row;
        }
        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => $tramslateText['title'],
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons
            ]),
        ]);
    }

    public function showCity($param)
    {
        $region_id = $param['r_id'];

        $tramslateText = $this->settingArray->arrSettingStartMenuCity[$this->language];

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
                    $current_row[] = ['text' => $name, 'callback_data' => Helper::arrayToString(['f' => 'platsbankenShowOccupation', 'c_id' => $id])];

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

        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => $tramslateText['title'],
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons
            ]),
        ]);
    }

    public function platsbankenShowOccupation($param)
    {

        $city_id = $param['c_id'];

        $translate = $param['trans'];

        $tramslateText = $this->settingArray->arrSettingStartMenuOccupation[$this->language];

        $occupation = $this->apiArbeits->getOccupation();
        if ($translate) {
            $translateApi = new TranslateApi();
            $occupation = Helper::translateData($occupation, $translateApi, $param['trans'], false);
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
            $current_row[] = ['text' => $name, 'callback_data' => Helper::arrayToString(['f' => 'showSpecialist', 'ok_id' => $id, 'c_id' => $city_id])];

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
            if ($this->language == 'uk') {
                $flag_unicode = "🇺🇦";
                $langParam = 'uk';
            } elseif ($this->language == 'ru') {
                $flag_unicode = "🇷🇺";
                $langParam = 'ru';
            } else {
                $flag_unicode = "🇬🇧";
                $langParam = 'en';
            }
            $buttons[] = [[
                'text' => $flag_unicode . ' ' . $tramslateText['buttonTranslate'],
                'callback_data' => Helper::arrayToString(['f' => 'platsbankenShowOccupation', 'c_id' => $city_id, 'trans' => $langParam])

            ]];
        }
        // Отправляем сообщение с кнопками
        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => $tramslateText['title'],
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons
            ]),
        ]);
    }

    public function showResult($param)
    {
        if (!empty($param['se_t'])) {
            $searchText = $param['se_t'];
            $searchText = $this->apiTranslate->translate($searchText, true);
            $searchText = strip_tags($searchText);
        } else {
            $city_id = $param['c_id'];
            $specialist_id = $param['spec_id'];
        }
        $tramslateText = $this->settingArray->arrSettingStartMenuResult[$this->language];
        if (isset($param['st_index'])) {
            $startIndex = $param['st_index'];
        } else {
            $startIndex = 0;
        }
        $getAll = $this->apiArbeits->showAll($startIndex, $city_id, $specialist_id, $searchText);

        $numberOfAds = $getAll['numberOfAds'];

        if ($numberOfAds == 0) {
            $this->telegram->sendMessage([
                'chat_id' => $this->chat_id,
                'text' => $tramslateText['notFount']
            ]);
            return;
        }

        $this->buildMenuFromAds($getAll, $this->chat_id, $this->telegram, $this->language);

        $totalPages = ceil($numberOfAds / 5);
        if ($totalPages == 1) {
            $inlineKeyboard = [];
        } else {
            // Создаем кнопки
            $inlineKeyboard = [];

            if (!empty($searchText)) {
                $left_button = ['text' => '←', 'callback_data' => Helper::arrayToString(['f' => 'showResult', 'st_index' => $startIndex - 5, 'se_t' => $searchText])];
                $right_button = ['text' => '→', 'callback_data' => Helper::arrayToString(['f' => 'showResult', 'st_index' => $startIndex + 5, 'se_t' => $searchText])];

            } else {
                $left_button = ['text' => '←', 'callback_data' => Helper::arrayToString(['f' => 'showResult', 'st_index' => $startIndex - 5, 'spec_id' => $specialist_id, 'c_id' => $city_id])];
                $right_button = ['text' => '→', 'callback_data' => Helper::arrayToString(['f' => 'showResult', 'st_index' => $startIndex + 5, 'spec_id' => $specialist_id, 'c_id' => $city_id])];

            }
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
            $this->telegram->sendMessage([
                'chat_id' => $this->chat_id,
                'text' => 'Выберите действие:',
                'reply_markup' => json_encode(['inline_keyboard' => [$inlineKeyboard]])
            ]);
        }
    }


    public function buildMenuFromAds($ads, $chatId, $objTelegram, $language)
    {
        $tramslateText = $this->settingArray->arrSettingbuildMenuFromAds[$language];

        foreach ($ads['ads'] as $ad) {
            // Создаем текст сообщения с полной информацией об объявлении
            $title = $ad['title'];
            $publishedDate = $ad['publishedDate'];
            $occupation = $ad['occupation'];
            $workplace = $ad['workplace'];
            $workplaceName = $ad['workplaceName'];
            $positions = $ad['positions'];

            $additionalInfo =
                "<b>" . $tramslateText['publishedDate'] . "</b> " . $publishedDate . "\n" .
                "<b>" . $tramslateText['occupation'] . "</b> " . $occupation . "\n" .
                "<b>" . $tramslateText['workplace'] . "</b> " . $workplace . "\n" .
                "<b>" . $tramslateText['workplaceName'] . "</b> " . $workplaceName . "\n" .
                "<b>" . $tramslateText['positions'] . "</b> " . $positions;

            // Создаем текст сообщения
            $messageText = "<b>$title</b>\n$additionalInfo";

            // Формируем кнопку "Подробнее" и кнопку "Скрыть" для каждого объявления
            $menu = [
                [
                    'text' => '⏬ ' . $tramslateText['details'],
                    'callback_data' => Helper::arrayToString(['f' => 'showOne', 'detail_id' => $ad['id']]),
                ],
                [
                    'text' => $tramslateText['Hide'],
                    'callback_data' => Helper::arrayToString(['f' => 'delMessage']),
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

    public function delMessage($param)
    {
        $messageId = $param['message_id'];
        $url = "https://api.telegram.org/bot{$_ENV['TELEGRAM_BOT_TOKEN']}/deleteMessage?chat_id={$this->chat_id}&message_id={$messageId}";
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_exec($ch);
        curl_close($ch);
    }

    public function showOne($param)
    {

        $key_board = $param['detail_id'];
        $tramslateText = $this->settingArray->arrSettingLanguage[$this->language];
        $ad = $this->apiArbeits->getOne($key_board);
        $str = Helper::processJobData($ad, $tramslateText);
        if ($param['trans']) {
            $str = $this->apiTranslate->translate($str, $param['trans']);
            $str = strip_tags($str);
        }
        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => $str,
            'parse_mode' => 'HTML', // Это для того, чтобы текст интерпретировался как HTML
        ]);

        if (!$param['trans']) {
            if ($this->language == 'uk') {
                $flag_unicode = "🇺🇦";
                $langParam = 'uk';
            } elseif ($this->language == 'ru') {
                $flag_unicode = "🇷🇺";
                $langParam = 'ru';
            } else {
                $flag_unicode = "🇬🇧";
                $langParam = 'en';
            }

            $this->telegram->sendMessage([
                'chat_id' => $this->chat_id,
                'text' => $flag_unicode . $tramslateText['btnTranslate'],
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => $flag_unicode . $tramslateText['btnTranslate'], 'callback_data' => Helper::arrayToString(['f' => 'showOne', 'detail_id' => $key_board, 'trans' => $langParam])]
                        ]
                    ]
                ]),
            ]);
        }
    }

    function sendLanguageMenu()
    {
        $ukrainianFlagUnicode = "🇺🇦";
        $russianFlagUnicode = "🇷🇺";
        $englishFlagUnicode = "🇬🇧";

        // Проверяем, установлен ли chat_id
        if ($this->chat_id) {
            $this->telegram->sendMessage([
                'chat_id' => $this->chat_id,
                'text' => 'Выберите язык:',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => $ukrainianFlagUnicode . ' Українська', 'callback_data' => Helper::arrayToString(['f' => 'startMenu', 'lang' => 'uk'])],
                            ['text' => $russianFlagUnicode . ' Русский', 'callback_data' => Helper::arrayToString(['f' => 'startMenu', 'lang' => 'ru'])],
                            ['text' => $englishFlagUnicode . ' English', 'callback_data' => Helper::arrayToString(['f' => 'startMenu', 'lang' => 'en'])]
                        ]
                    ]
                ]),
            ]);
        } else {
            // Обработка ошибки, например, вывод в лог или отправка уведомления
            error_log('Invalid chat_id: ' . $this->chat_id);
        }
    }
}