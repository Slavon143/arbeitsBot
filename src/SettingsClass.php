<?php

namespace src;

class SettingsClass
{
    public $arrSettingLanguage = [
//        'uk'=>[
//            'title' => 'Назва',
//            'sourceLinks_0_label'=>'Назва веб-сайту',
//            'sourceLinks_0_url'=>'Оголошення повністю читайте тут',
//            'btnTranslate'=>'Перекласти',
//            // 'id' => 'Ідентифікатор',
//            'description' => 'Опис',
//            'publishedDate' => 'Дата публікації',
//            'occupation' => 'Рід занять',
//            'company_name' => 'Назва компанії',
//            'company_streetAddress' => 'Вулиця компанії',
//            'company_postCode' => 'Поштовий індекс компанії',
//            'company_city' => 'Місто компанії',
//            'company_phoneNumber' => 'Телефон компанії',
//            'company_webAddress' => 'Веб-адреса компанії',
//            'company_email' => 'Email компанії',
//            'company_organisationNumber' => 'Організаційний номер компанії',
//            // 'logotype' => 'Логотип',
//            'conditions' => 'Умови',
//            'salaryDescription' => 'Опис заробітної плати',
//            'salaryType' => 'Тип заробітної плати',
//            // 'workTimeExtent' => 'Розширення робочого часу',
//            'employmentType' => 'Тип зайнятості',
//            'duration' => 'Тривалість',
//            'lastApplicationDate' => 'Остання дата подачі заявки',
//            'expirationDate' => 'Дата закінчення',
//            'positions' => 'Кількість позицій',
//            // 'published' => 'Опубліковано',
//            'ownCar' => 'Є автомобіль',
//            'requiresExperience' => 'Потрібний досвід',
//            'education_name' => 'Освіта',
//            'education_required' => 'Потрібна освіта',
//            'application_email' => 'Email заявки',
//            'application_webAddress' => 'Веб-адреса заявки',
//            'application_other' => 'Інша інформація про заявку',
//            'application_reference' => 'Посилання на заявку',
//            'application_information' => 'Додаткова інформація про заявку',
//            'workplace_name' => 'Назва місця роботи',
//            'workplace_street' => 'Вулиця місця роботи',
//            'workplace_postCode' => 'Поштовий індекс місця роботи',
//            'workplace_city' => 'Місто місця роботи',
//            'workplace_unspecifiedWorkplace' => 'Невизначене місце роботи',
//            'workplace_region' => 'Регіон місця роботи',
//            // 'workplace_country' => 'Країна місця роботи',
//            'workplace_municipality' => 'Муніципалітет місця роботи',
//            // 'workplace_longitude' => 'Довгота місця роботи',
//            // 'workplace_latitude' => 'Широта місця роботи',
//            'workplace_showMap' => 'Показати карту місця роботи',
//            'languages_0_name' => 'Назва мови',
//            'languages_0_required' => 'Потрібна мова',
//            'workExperiences_0_name' => 'Назва досвіду роботи',
//            'workExperiences_0_required' => 'Потрібний досвід роботи',
//            'contacts_0_name' => 'Ім\'я контактної особи 1',
//            'contacts_0_surname' => 'Прізвище контактної особи 1',
//            'contacts_0_position' => 'Посада контактної особи 1',
//            'contacts_0_mobileNumber' => 'Мобільний номер контактної особи 1',
//            'contacts_0_phoneNumber' => 'Номер телефону контактної особи 1',
//            'contacts_0_email' => 'Email контактної особи 1',
//            'contacts_0_union' => 'Приналежність до профспілки контактної особи 1',
//            'contacts_0_description' => 'Опис контактної особи 1',
//            'contacts_1_name' => 'Ім\'я контактної особи 2',
//            'contacts_1_surname' => 'Прізвище контактної особи 2',
//            'contacts_1_position' => 'Посада контактної особи 2',
//            'contacts_1_mobileNumber' => 'Мобільний номер контактної особи 2',
//            'contacts_1_phoneNumber' => 'Номер телефону контактної особи 2',
//            'contacts_1_email' => 'Email контактної особи 2',
//            'contacts_1_union' => 'Приналежність до профспілки контактної особи 2',
//            'contacts_1_description' => 'Опис контактної особи 2',
//            'skills_0_name' => 'Назва навички 1',
//            'skills_0_required' => 'Потрібна навичка 1',
//            'skills_1_name' => 'Назва навички 2',
//            'skills_1_required' => 'Потрібна навичка 2',
//            'skills_2_name' => 'Назва навички 3',
//            'skills_2_required' => 'Потрібна навичка 3',
//        ],
//        'ru'=>[
//            'title' => 'Название',
//            'sourceLinks_0_label'=>'Название веб-сайта',
//            'sourceLinks_0_url'=>'Объявление полностью читайте здесь',
//            'btnTranslate'=>'Перевести',
////            'id' => 'Идентификатор',
//
//            'description' => 'Описание',
//            'publishedDate' => 'Дата публикации',
//            'occupation' => 'Род занятий',
//            'company_name' => 'Название компании',
//            'company_streetAddress' => 'Улица компании',
//            'company_postCode' => 'Почтовый индекс компании',
//            'company_city' => 'Город компании',
//            'company_phoneNumber' => 'Телефон компании',
//            'company_webAddress' => 'Веб-адрес компании',
//            'company_email' => 'Email компании',
//            'company_organisationNumber' => 'Организационный номер компании',
////            'logotype' => 'Логотип',
//            'conditions' => 'Условия',
//            'salaryDescription' => 'Описание зарплаты',
//            'salaryType' => 'Тип зарплаты',
////            'workTimeExtent' => 'Расширение рабочего времени',
//            'employmentType' => 'Тип занятости',
//            'duration' => 'Продолжительность',
//            'lastApplicationDate' => 'Последняя дата подачи заявки',
//            'expirationDate' => 'Дата окончания',
//            'positions' => 'Количество позиций',
////            'published' => 'Опубликовано',
//            'ownCar' => 'Есть автомобиль',
//            'requiresExperience' => 'Требуется опыт',
//            'education_name' => 'Образование',
//            'education_required' => 'Требуется образование',
//            'application_email' => 'Email заявки',
//            'application_webAddress' => 'Веб-адрес заявки',
//            'application_other' => 'Другая информация о заявке',
//            'application_reference' => 'Ссылка на заявку',
//            'application_information' => 'Дополнительная информация о заявке',
//            'workplace_name' => 'Название места работы',
//            'workplace_street' => 'Улица места работы',
//            'workplace_postCode' => 'Почтовый индекс места работы',
//            'workplace_city' => 'Город места работы',
//            'workplace_unspecifiedWorkplace' => 'Неуказанное место работы',
//            'workplace_region' => 'Регион места работы',
////            'workplace_country' => 'Страна места работы',
//            'workplace_municipality' => 'Муниципалитет места работы',
////            'workplace_longitude' => 'Долгота места работы',
////            'workplace_latitude' => 'Широта места работы',
//            'workplace_showMap' => 'Показать карту места работы',
//            'languages_0_name' => 'Название языка',
//            'languages_0_required' => 'Требуется язык',
//            'workExperiences_0_name' => 'Название опыта работы',
//            'workExperiences_0_required' => 'Требуется опыт работы',
//            'contacts_0_name' => 'Имя контактного лица 1',
//            'contacts_0_surname' => 'Фамилия контактного лица 1',
//            'contacts_0_position' => 'Должность контактного лица 1',
//            'contacts_0_mobileNumber' => 'Мобильный номер контактного лица 1',
//            'contacts_0_phoneNumber' => 'Номер телефона контактного лица 1',
//            'contacts_0_email' => 'Email контактного лица 1',
//            'contacts_0_union' => 'Принадлежность к профсоюзу контактного лица 1',
//            'contacts_0_description' => 'Описание контактного лица 1',
//            'contacts_1_name' => 'Имя контактного лица 2',
//            'contacts_1_surname' => 'Фамилия контактного лица 2',
//            'contacts_1_position' => 'Должность контактного лица 2',
//            'contacts_1_mobileNumber' => 'Мобильный номер контактного лица 2',
//            'contacts_1_phoneNumber' => 'Номер телефона контактного лица 2',
//            'contacts_1_email' => 'Email контактного лица 2',
//            'contacts_1_union' => 'Принадлежность к профсоюзу контактного лица 2',
//            'contacts_1_description' => 'Описание контактного лица 2',
//            'skills_0_name' => 'Название навыка 1',
//            'skills_0_required' => 'Требуется навык 1',
//            'skills_1_name' => 'Название навыка 2',
//            'skills_1_required' => 'Требуется навык 2',
//            'skills_2_name' => 'Название навыка 3',
//            'skills_2_required' => 'Требуется навык 3',
//        ],
//        'en'=>[
//            'title' => 'Title',
//            'sourceLinks_0_label'=>'Website name',
//            'sourceLinks_0_url'=>'Read the full announcement here',
//            'btnTranslate'=>'Translate',
//            'description' => 'Description',
//            'publishedDate' => 'Published Date',
//            'occupation' => 'Occupation',
//            'company_name' => 'Company Name',
//            'company_streetAddress' => 'Company Street Address',
//            'company_postCode' => 'Company Post Code',
//            'company_city' => 'Company City',
//            'company_phoneNumber' => 'Company Phone Number',
//            'company_webAddress' => 'Company Web Address',
//            'company_email' => 'Company Email',
//            'company_organisationNumber' => 'Company Organisation Number',
//            'conditions' => 'Conditions',
//            'salaryDescription' => 'Salary Description',
//            'salaryType' => 'Salary Type',
//            'employmentType' => 'Employment Type',
//            'duration' => 'Duration',
//            'lastApplicationDate' => 'Last Application Date',
//            'expirationDate' => 'Expiration Date',
//            'positions' => 'Positions',
//            'ownCar' => 'Own Car',
//            'requiresExperience' => 'Requires Experience',
//            'education_name' => 'Education Name',
//            'education_required' => 'Education Required',
//            'application_email' => 'Application Email',
//            'application_webAddress' => 'Application Web Address',
//            'application_other' => 'Application Other',
//            'application_reference' => 'Application Reference',
//            'application_information' => 'Application Information',
//            'workplace_name' => 'Workplace Name',
//            'workplace_street' => 'Workplace Street',
//            'workplace_postCode' => 'Workplace Post Code',
//            'workplace_city' => 'Workplace City',
//            'workplace_unspecifiedWorkplace' => 'Unspecified Workplace',
//            'workplace_region' => 'Workplace Region',
//            'workplace_municipality' => 'Workplace Municipality',
//            'workplace_showMap' => 'Show Map',
//            'languages_0_name' => 'Languages Name',
//            'languages_0_required' => 'Languages Required',
//            'workExperiences_0_name' => 'Work Experiences Name',
//            'workExperiences_0_required' => 'Work Experiences Required',
//            'contacts_0_name' => 'Contacts Name',
//            'contacts_0_surname' => 'Contacts Surname',
//            'contacts_0_position' => 'Contacts Position',
//            'contacts_0_mobileNumber' => 'Contacts Mobile Number',
//            'contacts_0_phoneNumber' => 'Contacts Phone Number',
//            'contacts_0_email' => 'Contacts Email',
//            'contacts_0_union' => 'Contacts Union',
//            'contacts_0_description' => 'Contacts Description',
//            'contacts_1_name' => 'Contacts Name',
//            'contacts_1_surname' => 'Contacts Surname',
//            'contacts_1_position' => 'Contacts Position',
//            'contacts_1_mobileNumber' => 'Contacts Mobile Number',
//            'contacts_1_phoneNumber' => 'Contacts Phone Number',
//            'contacts_1_email' => 'Contacts Email',
//            'contacts_1_union' => 'Contacts Union',
//            'contacts_1_description' => 'Contacts Description',
//            'skills_0_name' => 'Skills Name',
//            'skills_0_required' => 'Skills Required',
//            'skills_1_name' => 'Skills Name',
//            'skills_1_required' => 'Skills Required',
//            'skills_2_name' => 'Skills Name',
//            'skills_2_required' => 'Skills Required',
//        ],
        'sv'=>[
            'title' => 'Titel',
            'sourceLinks_0_label'=>'Webbplatsnamn',
            'sourceLinks_0_url'=>'Läs hela annonsen här',
            'btnTranslate'=>'Översätt',
            'description' => 'Beskrivning',
            'publishedDate' => 'Publiceringsdatum',
            'occupation' => 'Yrke',
            'company_name' => 'Företagsnamn',
            'company_streetAddress' => 'Företagets gatuadress',
            'company_postCode' => 'Företagets postnummer',
            'company_city' => 'Företagets stad',
            'company_phoneNumber' => 'Företagets telefonnummer',
            'company_webAddress' => 'Företagets webbadress',
            'company_email' => 'Företagets e-post',
            'company_organisationNumber' => 'Företagets organisationsnummer',
            'conditions' => 'Villkor',
            'salaryDescription' => 'Lönebeskrivning',
            'salaryType' => 'Lönetyp',
            'employmentType' => 'Anställningstyp',
            'duration' => 'Varaktighet',
            'lastApplicationDate' => 'Sista ansökningsdatum',
            'expirationDate' => 'Utgångsdatum',
            'positions' => 'Positioner',
            'ownCar' => 'Egen bil',
            'requiresExperience' => 'Kräver erfarenhet',
            'education_name' => 'Utbildningsnamn',
            'education_required' => 'Utbildning krävs',
            'application_email' => 'Ansöknings-e-post',
            'application_webAddress' => 'Ansökningswebbadress',
            'application_other' => 'Annat för ansökan',
            'application_reference' => 'Ansökningsreferens',
            'application_information' => 'Ansökningsinformation',
            'workplace_name' => 'Arbetsplatsens namn',
            'workplace_street' => 'Arbetsplatsens gata',
            'workplace_postCode' => 'Arbetsplatsens postnummer',
            'workplace_city' => 'Arbetsplatsens stad',
            'workplace_unspecifiedWorkplace' => 'Ej specificerad arbetsplats',
            'workplace_region' => 'Arbetsplatsens region',
            'workplace_municipality' => 'Arbetsplatsens kommun',
            'workplace_showMap' => 'Visa karta',
            'languages_0_name' => 'Språkets namn',
            'languages_0_required' => 'Språk krävs',
            'workExperiences_0_name' => 'Arbetserfarenhetens namn',
            'workExperiences_0_required' => 'Arbetserfarenhet krävs',
            'contacts_0_name' => 'Kontaktpersonens namn',
            'contacts_0_surname' => 'Kontaktpersonens efternamn',
            'contacts_0_position' => 'Kontaktpersonens befattning',
            'contacts_0_mobileNumber' => 'Kontaktpersonens mobilnummer',
            'contacts_0_phoneNumber' => 'Kontaktpersonens telefonnummer',
            'contacts_0_email' => 'Kontaktpersonens e-post',
            'contacts_0_union' => 'Kontaktpersonens fackförening',
            'contacts_0_description' => 'Kontaktpersonens beskrivning',
            'contacts_1_name' => 'Kontaktpersonens namn',
            'contacts_1_surname' => 'Kontaktpersonens efternamn',
            'contacts_1_position' => 'Kontaktpersonens befattning',
            'contacts_1_mobileNumber' => 'Kontaktpersonens mobilnummer',
            'contacts_1_phoneNumber' => 'Kontaktpersonens telefonnummer',
            'contacts_1_email' => 'Kontaktpersonens e-post',
            'contacts_1_union' => 'Kontaktpersonens fackförening',
            'contacts_1_description' => 'Kontaktpersonens beskrivning',
            'skills_0_name' => 'Färdighetens namn',
            'skills_0_required' => 'Färdighet krävs',
            'skills_1_name' => 'Färdighetens namn',
            'skills_1_required' => 'Färdighet krävs',
            'skills_2_name' => 'Färdighetens namn',
            'skills_2_required' => 'Färdighet krävs',
        ],

    ];
    public $arrSettingStartMenuRegion = [
        'uk' => [
            'title' => 'Виберіть регіон:'
        ],
        'ru'=>[
            'title'=>'Выберите регион:'
        ],
        'en'=>[
            'title'=>'Select a region:'
        ]
    ];
    public $arrSettingStartMenuSpecialist = [
        'uk' => [
            'title' => 'Виберіть спеціальність:',
            'buttonTranslate'=>'Перекласти:'
        ],
        'ru'=>[
            'title'=>'Выберите специальность:',
            'buttonTranslate'=>'Перевести:'
        ],
        'en'=>[
            'title'=>'Select specialty:',
            'buttonTranslate'=>'Translate:'
        ]
    ];

    public $btnTranslate = [
        'uk' =>[
            'trans' => 'Перекласти'
        ],
        'ru' =>[
            'trans' => 'Перевести'
        ],
        'en' =>[
            'trans' => 'Translate'
        ],
    ];

    public $btnHide = [
        'uk' =>[
            'Hide' => 'Приховати'
        ],
        'ru' =>[
            'Hide' => 'Скрыть'
        ],
        'en' =>[
            'Hide' => 'Hide'
        ],
    ];
    public $arrSettingbuildMenuFromAds = [
        'uk' => [
            'publishedDate' => 'Дата публікації:',
            'occupation' => 'Професія:',
            'workplace' => 'Місце роботи:',
            'workplaceName' => 'Назва місця роботи:',
            'positions' => 'Кількість позицій:',
            'details' => 'Детальніше',
            'Hide' => 'Приховати',
        ],
        'ru'=>[
            'publishedDate' => 'Дата публикации:',
            'occupation' => 'Профессия:',
            'workplace' => 'Место работы:',
            'workplaceName' => 'Название места работы:',
            'positions' => 'Количество позиций:',
            'details' => 'Подробнее',
            'Hide' => 'Скрыть',
        ],
        'en'=>[
            'publishedDate' => 'Publication date:',
            'occupation' => 'Profession:',
            'workplace' => 'Place of work:',
            'workplaceName' => 'Job name:',
            'positions' => 'Number of positions:',
            'details' => 'More details',
            'Hide' => 'Hide',
        ],
        'sv'=>[
            'publishedDate' => 'Publiceringsdatum:',
            'occupation' => 'Yrke:',
            'workplace' => 'Arbetsplats:',
            'workplaceName' => 'Jobbnamn:',
            'positions' => 'Antal positioner:',
            'details' => 'Mer detaljer',
            'Hide' => 'Dölj',
        ]
    ];
    public $arrSettingStartMenuResult = [
        'uk' => [
            'notFount' => 'На жаль, оголошень не знайдено.',
        ],
        'ru'=>[
            'notFount'=>'К сожалению, объявлений не найдено.'
        ],
        'en'=>[
            'notFount'=>'Sorry, no advertisements found.',
        ]
    ];
    public $arrSettingStartMenu = [
        'uk'=>[
            'title'=>'Зробіть вибір ресурсів:',
            'platsbankenButton' => 'Банк локацій:',
            'webbplatserButton'=>'Зовнішні сайти:'
        ],
        'ru'=>[
            'title'=>'Сделайте выбор ресурсов:',
            'platsbankenButton' => 'Банк локаций:',
            'webbplatserButton'=>'Внешние сайты:'
        ],
        'en' =>[
            'title'=>'Make a selection of resources:',
            'platsbankenButton' => 'Location bank',
            'webbplatserButton'=>'External sites:'
        ]
    ];
    public $arrSettingStartMenuOccupation = [
        'uk' => [
            'title' => 'Виберіть напрямок:',
            'buttonTranslate'=>'Перекласти:'
        ],
        'ru'=>[
            'title'=>'Выберите направление:',
            'buttonTranslate'=>'Перевести:'
        ],
        'en'=>[
            'title'=>'Select direction:',
            'buttonTranslate'=>'Translate:'
        ]
    ];
    public $arrSettingStartMenuCity = [
        'uk' => [
            'title' => 'Виберіть місто:'
        ],
        'ru'=>[
            'title'=>'Выберите город:'
        ],
        'en'=>[
            'title'=>'Select city:'
        ]
    ];

    public $btnSendLanguageMenu = [
        'uk'=>[
            'chooseLanguage'=>'Виберіть мову'
        ],
        'ru'=>[
            'chooseLanguage' => 'Выберите язык'
        ],
        'en'=>[
            'chooseLanguage'=>'Choose language'
        ],
    ];
}