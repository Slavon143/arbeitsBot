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

    public function startMenu($data)
    {
        $tramslateText = '';
        if ($data){
            $tramslateText = $this->settingArray->arrSettingStartMenu[$data];
        }else{
            $tramslateText = $this->settingArray->arrSettingStartMenu[$this->language];
        }
        $this->nawMenu($data);

        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => ' 🔄 ' .'<b>'. $tramslateText['title'] .'</b>',
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => $tramslateText['platsbankenButton'] . ' ➡️', 'callback_data' => Helper::arrayToString(['f' => 'showRegion', 'resource' => 'pb'])],
                        ['text' => $tramslateText['webbplatserButton'] . ' ➡️', 'callback_data' => Helper::arrayToString(['f' => 'showRegion', 'resource' => 'joblinks'])],
                    ]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ]);
    }

    public function showRegion($param)
    {
        if (isset($param['resource'])) {
            $this->db->recordResourceChoice($this->chat_id, $param['resource']);
        }
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
            'text' => ' 📍 '. '<b>'.$tramslateText['title'] .'</b>',
            'parse_mode' => 'HTML',
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
        $columns = 3;
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
        if (!empty($current_row)) {
            $buttons[] = $current_row;
        }
        $buttons[] = [['text' => $this->settingArray->arrSettingStartMenuCity[$this->language]['btnShowAll'], 'callback_data' => Helper::arrayToString(['f' => 'platsbankenShowOccupation', 'r_id' => $region_id])]];
        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => '🏰 ' . '<b>' . $tramslateText['title'] .'</b>',
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons
            ]),
        ]);
    }

    public function platsbankenShowOccupation($param)
    {
        $location = [];
        if (isset($param['r_id'])) {
            $location['r_id'] = $param['r_id'];
        } elseif (isset($param['c_id'])) {
            $location['c_id'] = $param['c_id'];
        }

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
            $current_row[] = ['text' => $name, 'callback_data' => Helper::arrayToString(['f' => 'showSpecialist', 'ok_id' => $id] + $location)];

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
            $flag_unicode = Helper::getFlag($this->language);
            $buttons[] = [[
                'text' => $flag_unicode . ' ' . $tramslateText['buttonTranslate'],
                'callback_data' => Helper::arrayToString(['f' => 'platsbankenShowOccupation', 'trans' => $this->language] + $location)

            ]];
        }
        // Отправляем сообщение с кнопками
        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => '<b>' . $tramslateText['title'] .'</b>',
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons
            ]),
        ]);
    }

    public function showSpecialist($param)
    {
        $occupation_id = $param['ok_id'];

        $location = [];
        if (isset($param['r_id'])) {
            $location['r_id'] = $param['r_id'];
        } elseif (isset($param['c_id'])) {
            $location['c_id'] = $param['c_id'];
        }

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
                $row[] = ['text' => $name, 'callback_data' => Helper::arrayToString(['f' => 'showResult', 'spec_id' => $id] + $location)];

                // Если текущий ряд заполнен, добавляем его в массив кнопок и создаем новый ряд
                if (count($row) >= $columns) {
                    $buttons[] = $row;
                    $row = [];
                }
            }
            $buttons[] = [['text' => $this->settingArray->arrSettingStartMenuCity[$this->language]['btnShowAll'], 'callback_data' => Helper::arrayToString(['f' => 'showResult', 'spec_all' => $occupation_id]+$location)]];
        } else {
            foreach ($occupation as $item) {
                if ($item['id'] == $occupation_id) {
                    foreach ($item['items'] as $profession) {
                        $id = $profession['id'];
                        $name = $profession['name'];

                        // Добавляем кнопку в текущую строку
                        $current_row[] = ['text' => $name, 'callback_data' => Helper::arrayToString(['f' => 'showResult', 'spec_id' => $id] + $location)];

                        // Увеличиваем счетчик текущей колонки
                        $current_column++;

                        // Если текущая колонка достигла максимальной ширины, добавляем текущую строку в массив кнопок и создаем новую строку
                        if ($current_column >= $columns) {
                            $buttons[] = $current_row;
                            $current_row = [];
                            $current_column = 0;
                        }
                    }
                    $buttons[] = [['text' => $this->settingArray->arrSettingStartMenuCity[$this->language]['btnShowAll'], 'callback_data' => Helper::arrayToString(['f' => 'showResult', 'spec_all' => $occupation_id]+$location)]];
                }
            }
            $flag_unicode = Helper::getFlag($this->language);
            $buttons[] = [[
                'text' => $flag_unicode . ' ' . $tramslateText['buttonTranslate'],
                'callback_data' => Helper::arrayToString(['f' => 'showSpecialist', 'ok_id' => $occupation_id,  'trans' => $this->language] + $location)
            ]];
        }
        if (!empty($current_row)) {
            $buttons[] = $current_row;
        }
        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => '<b>' . $tramslateText['title'] .'</b>',
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                'inline_keyboard' => $buttons
            ]),
        ]);
    }


    public function showResult($param)
    {
        $location = [];
        if (isset($param['r_id'])) {
            $location['r_id'] = $param['r_id'];
        }
        if (isset($param['c_id'])) {
            $location['c_id'] = $param['c_id'];
        }
        if (isset($param['spec_id'])) {
            $location['spec_id'] = $param['spec_id'];
        }
        if (isset($param['spec_all'])) {
            $location['spec_all'] = $param['spec_all'];
        }
        if (isset($param['se_t'])) {
            $param['se_t'] = $this->apiTranslate->translate($param['se_t'], '', true);
            $param['se_t'] = strip_tags($param['se_t']);
            $location['se_t'] = $param['se_t'];
        }

        $resource = $this->db->getResourceChoices($this->chat_id);

        if (!$resource) {
            $resource = 'pb';
        }

        $tramslateText = $this->settingArray->arrSettingStartMenuResult[$this->language];

        if (isset($param['st_index'])) {
            $startIndex = $param['st_index'];
        } else {
            $startIndex = 0;
        }

        $getAll = $this->apiArbeits->showAll($startIndex, $resource,$param);

        $numberOfAds = $getAll['numberOfAds'];

        if ($numberOfAds == 0) {
            $this->telegram->sendMessage([
                'chat_id' => $this->chat_id,
                'text' => $tramslateText['notFount']
            ]);
            return; // Не выводим клавиатуру, так как нет результатов
        }

        $this->buildMenuFromAds($getAll);

        $totalPages = ceil($numberOfAds / 5);

        if ($totalPages > 1) {
            // Создаем кнопки
            $inlineKeyboard = [];

            $left_button = ['text' => '←', 'callback_data' => Helper::arrayToString(['f' => 'showResult', 'st_index' => $startIndex - 5] + $location)];
            $right_button = ['text' => '→', 'callback_data' => Helper::arrayToString(['f' => 'showResult', 'st_index' => $startIndex + 5] + $location)];

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
                'text' => $this->settingArray->arrSettingStartMenu[$this->language]['btnNavigatePage'],
                'reply_markup' => json_encode(['inline_keyboard' => [$inlineKeyboard]])
            ]);
        }
    }

    public function buildMenuFromAds($ads)
    {
        $tramslateText = $this->settingArray->arrSettingbuildMenuFromAds['sv'];

        foreach ($ads['ads'] as $ad) {

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

            $flag_unicode = Helper::getFlag($this->language);

            $menu = [
                [
                    'text' => '⏬ ' . $this->settingArray->arrSettingbuildMenuFromAds[$this->language]['details'],
                    'callback_data' => Helper::arrayToString(['f' => 'showOne', 'detail_id' => $ad['id']]),
                ],
                [
                    'text' => $this->settingArray->arrSettingbuildMenuFromAds[$this->language]['Hide'],
                    'callback_data' => Helper::arrayToString(['f' => 'delMessage']),
                ],
                ['text' => $flag_unicode . $this->settingArray->btnTranslate[$this->language]['trans'], 'callback_data' => Helper::arrayToString(['f' => 'showOne', 'detail_id' => $ad['id'], 'trans' => $this->language])]
            ];

            // Отправляем сообщение с полной информацией об объявлении и кнопкой "Подробнее" и "Скрыть"
            $this->telegram->sendMessage([
                'chat_id' => $this->chat_id,
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
        $resource = $this->db->getResourceChoices($this->chat_id);

        $key_board = $param['detail_id'];
        $tramslateText = $this->settingArray->arrSettingLanguage['sv'];

        $ad = $this->apiArbeits->getOne($key_board, $resource);

        $str = Helper::processJobData($ad, $tramslateText);

        if ($param['trans']) {
            $str = $this->apiTranslate->translate($str, $this->language, false);
            $str = strip_tags($str);
        }

        $flag_unicode = Helper::getFlag($this->language);
        $menu = [];
        if (!$param['trans']) {
            $menu[] = [
                ['text' => $flag_unicode . $this->settingArray->btnTranslate[$this->language]['trans'], 'callback_data' => Helper::arrayToString(['f' => 'showOne', 'detail_id' => $key_board, 'trans' => $this->language])],
                ['text' => $this->settingArray->btnHide[$this->language]['Hide'], 'callback_data' => Helper::arrayToString(['f' => 'delMessage'])]
            ];
        } else {
            $menu[] = [
                ['text' => $this->settingArray->btnHide[$this->language]['Hide'], 'callback_data' => Helper::arrayToString(['f' => 'delMessage'])]
            ];
        }
        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => $str,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['inline_keyboard' => $menu]),
        ]);
    }

    function sendLanguageMenu()
    {
        $ukrainianFlagUnicode = "🇺🇦";
        $russianFlagUnicode = "🇷🇺";
        $englishFlagUnicode = "🇬🇧";

        if ($this->language){
            $chooseLanguage = $this->settingArray->btnSendLanguageMenu[$this->language]['chooseLanguage'];
        }else{
            $chooseLanguage = $this->settingArray->btnSendLanguageMenu['ru']['chooseLanguage'];
        }

        if ($this->chat_id) {
            $this->telegram->sendMessage([
                'chat_id' => $this->chat_id,
                'text' => $chooseLanguage,
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

    public function nawMenu($data){

        $lang = '';
        if ($data){
            $lang = $data;
        }else{
            $lang = $this->language;
        }

        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => ' ...',
            'reply_markup' => json_encode([
                'keyboard' => [
                    [
                        ['text' => $this->settingArray->arrSettingStartMenuRegion[$lang]['title']],
                        ['text' => '🏠 ' . $this->settingArray->btnNawTranslate[$lang]['startTitle']],
                        ['text' => $this->settingArray->arrSettingStartMenuOccupation[$lang]['title']],
                    ],
                    [
                        ['text' => '🔙 ' . $this->settingArray->btnNawTranslate[$lang]['startBack']],
                        ['text' => '🌐 ' . $this->settingArray->btnNawTranslate[$lang]['startLanguage']],

                    ],
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => false
            ])
        ]);
    }

    public function menuHelp(){
        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'parse_mode' => 'HTML',
            'text' => $this->settingArray->arrSuggestSetting[$this->language]['suggestMessage'],
        ]);
    }
    public function sendMeMessage($message){
        $this->telegram->sendMessage([
            'chat_id' => $_ENV['MY_CHAT_ID'],
            'text' => $message . ' ChatId ' . $this->chat_id,
        ]);
        die();
    }
}