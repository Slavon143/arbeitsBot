<?php

namespace src;

class SettingsClass
{
    public $arrSettingLanguage = [
        'language_ukrainian'=>[
            // 'id' => 'Ідентифікатор',
            'title' => 'Назва',
            'description' => 'Опис',
            'publishedDate' => 'Дата публікації',
            'occupation' => 'Рід занять',
            'company_name' => 'Назва компанії',
            'company_streetAddress' => 'Вулиця компанії',
            'company_postCode' => 'Поштовий індекс компанії',
            'company_city' => 'Місто компанії',
            'company_phoneNumber' => 'Телефон компанії',
            'company_webAddress' => 'Веб-адреса компанії',
            'company_email' => 'Email компанії',
            'company_organisationNumber' => 'Організаційний номер компанії',
            // 'logotype' => 'Логотип',
            'conditions' => 'Умови',
            'salaryDescription' => 'Опис заробітної плати',
            'salaryType' => 'Тип заробітної плати',
            // 'workTimeExtent' => 'Розширення робочого часу',
            'employmentType' => 'Тип зайнятості',
            'duration' => 'Тривалість',
            'lastApplicationDate' => 'Остання дата подачі заявки',
            'expirationDate' => 'Дата закінчення',
            'positions' => 'Кількість позицій',
            // 'published' => 'Опубліковано',
            'ownCar' => 'Є автомобіль',
            'requiresExperience' => 'Потрібний досвід',
            'education_name' => 'Освіта',
            'education_required' => 'Потрібна освіта',
            'application_email' => 'Email заявки',
            'application_webAddress' => 'Веб-адреса заявки',
            'application_other' => 'Інша інформація про заявку',
            'application_reference' => 'Посилання на заявку',
            'application_information' => 'Додаткова інформація про заявку',
            'workplace_name' => 'Назва місця роботи',
            'workplace_street' => 'Вулиця місця роботи',
            'workplace_postCode' => 'Поштовий індекс місця роботи',
            'workplace_city' => 'Місто місця роботи',
            'workplace_unspecifiedWorkplace' => 'Невизначене місце роботи',
            'workplace_region' => 'Регіон місця роботи',
            // 'workplace_country' => 'Країна місця роботи',
            'workplace_municipality' => 'Муніципалітет місця роботи',
            // 'workplace_longitude' => 'Довгота місця роботи',
            // 'workplace_latitude' => 'Широта місця роботи',
            'workplace_showMap' => 'Показати карту місця роботи',
            'languages_0_name' => 'Назва мови',
            'languages_0_required' => 'Потрібна мова',
            'workExperiences_0_name' => 'Назва досвіду роботи',
            'workExperiences_0_required' => 'Потрібний досвід роботи',
            'contacts_0_name' => 'Ім\'я контактної особи 1',
            'contacts_0_surname' => 'Прізвище контактної особи 1',
            'contacts_0_position' => 'Посада контактної особи 1',
            'contacts_0_mobileNumber' => 'Мобільний номер контактної особи 1',
            'contacts_0_phoneNumber' => 'Номер телефону контактної особи 1',
            'contacts_0_email' => 'Email контактної особи 1',
            'contacts_0_union' => 'Приналежність до профспілки контактної особи 1',
            'contacts_0_description' => 'Опис контактної особи 1',
            'contacts_1_name' => 'Ім\'я контактної особи 2',
            'contacts_1_surname' => 'Прізвище контактної особи 2',
            'contacts_1_position' => 'Посада контактної особи 2',
            'contacts_1_mobileNumber' => 'Мобільний номер контактної особи 2',
            'contacts_1_phoneNumber' => 'Номер телефону контактної особи 2',
            'contacts_1_email' => 'Email контактної особи 2',
            'contacts_1_union' => 'Приналежність до профспілки контактної особи 2',
            'contacts_1_description' => 'Опис контактної особи 2',
            'skills_0_name' => 'Назва навички 1',
            'skills_0_required' => 'Потрібна навичка 1',
            'skills_1_name' => 'Назва навички 2',
            'skills_1_required' => 'Потрібна навичка 2',
            'skills_2_name' => 'Назва навички 3',
            'skills_2_required' => 'Потрібна навичка 3',
        ],
        'language_russian'=>[
//            'id' => 'Идентификатор',
            'title' => 'Название',
            'description' => 'Описание',
            'publishedDate' => 'Дата публикации',
            'occupation' => 'Род занятий',
            'company_name' => 'Название компании',
            'company_streetAddress' => 'Улица компании',
            'company_postCode' => 'Почтовый индекс компании',
            'company_city' => 'Город компании',
            'company_phoneNumber' => 'Телефон компании',
            'company_webAddress' => 'Веб-адрес компании',
            'company_email' => 'Email компании',
            'company_organisationNumber' => 'Организационный номер компании',
//            'logotype' => 'Логотип',
            'conditions' => 'Условия',
            'salaryDescription' => 'Описание зарплаты',
            'salaryType' => 'Тип зарплаты',
//            'workTimeExtent' => 'Расширение рабочего времени',
            'employmentType' => 'Тип занятости',
            'duration' => 'Продолжительность',
            'lastApplicationDate' => 'Последняя дата подачи заявки',
            'expirationDate' => 'Дата окончания',
            'positions' => 'Количество позиций',
//            'published' => 'Опубликовано',
            'ownCar' => 'Есть автомобиль',
            'requiresExperience' => 'Требуется опыт',
            'education_name' => 'Образование',
            'education_required' => 'Требуется образование',
            'application_email' => 'Email заявки',
            'application_webAddress' => 'Веб-адрес заявки',
            'application_other' => 'Другая информация о заявке',
            'application_reference' => 'Ссылка на заявку',
            'application_information' => 'Дополнительная информация о заявке',
            'workplace_name' => 'Название места работы',
            'workplace_street' => 'Улица места работы',
            'workplace_postCode' => 'Почтовый индекс места работы',
            'workplace_city' => 'Город места работы',
            'workplace_unspecifiedWorkplace' => 'Неуказанное место работы',
            'workplace_region' => 'Регион места работы',
//            'workplace_country' => 'Страна места работы',
            'workplace_municipality' => 'Муниципалитет места работы',
//            'workplace_longitude' => 'Долгота места работы',
//            'workplace_latitude' => 'Широта места работы',
            'workplace_showMap' => 'Показать карту места работы',
            'languages_0_name' => 'Название языка',
            'languages_0_required' => 'Требуется язык',
            'workExperiences_0_name' => 'Название опыта работы',
            'workExperiences_0_required' => 'Требуется опыт работы',
            'contacts_0_name' => 'Имя контактного лица 1',
            'contacts_0_surname' => 'Фамилия контактного лица 1',
            'contacts_0_position' => 'Должность контактного лица 1',
            'contacts_0_mobileNumber' => 'Мобильный номер контактного лица 1',
            'contacts_0_phoneNumber' => 'Номер телефона контактного лица 1',
            'contacts_0_email' => 'Email контактного лица 1',
            'contacts_0_union' => 'Принадлежность к профсоюзу контактного лица 1',
            'contacts_0_description' => 'Описание контактного лица 1',
            'contacts_1_name' => 'Имя контактного лица 2',
            'contacts_1_surname' => 'Фамилия контактного лица 2',
            'contacts_1_position' => 'Должность контактного лица 2',
            'contacts_1_mobileNumber' => 'Мобильный номер контактного лица 2',
            'contacts_1_phoneNumber' => 'Номер телефона контактного лица 2',
            'contacts_1_email' => 'Email контактного лица 2',
            'contacts_1_union' => 'Принадлежность к профсоюзу контактного лица 2',
            'contacts_1_description' => 'Описание контактного лица 2',
            'skills_0_name' => 'Название навыка 1',
            'skills_0_required' => 'Требуется навык 1',
            'skills_1_name' => 'Название навыка 2',
            'skills_1_required' => 'Требуется навык 2',
            'skills_2_name' => 'Название навыка 3',
            'skills_2_required' => 'Требуется навык 3',
        ],
        'language_english'=>[
            'title' => 'Title',
            'description' => 'Description',
            'publishedDate' => 'Published Date',
            'occupation' => 'Occupation',
            'company_name' => 'Company Name',
            'company_streetAddress' => 'Company Street Address',
            'company_postCode' => 'Company Post Code',
            'company_city' => 'Company City',
            'company_phoneNumber' => 'Company Phone Number',
            'company_webAddress' => 'Company Web Address',
            'company_email' => 'Company Email',
            'company_organisationNumber' => 'Company Organisation Number',
            'conditions' => 'Conditions',
            'salaryDescription' => 'Salary Description',
            'salaryType' => 'Salary Type',
            'employmentType' => 'Employment Type',
            'duration' => 'Duration',
            'lastApplicationDate' => 'Last Application Date',
            'expirationDate' => 'Expiration Date',
            'positions' => 'Positions',
            'ownCar' => 'Own Car',
            'requiresExperience' => 'Requires Experience',
            'education_name' => 'Education Name',
            'education_required' => 'Education Required',
            'application_email' => 'Application Email',
            'application_webAddress' => 'Application Web Address',
            'application_other' => 'Application Other',
            'application_reference' => 'Application Reference',
            'application_information' => 'Application Information',
            'workplace_name' => 'Workplace Name',
            'workplace_street' => 'Workplace Street',
            'workplace_postCode' => 'Workplace Post Code',
            'workplace_city' => 'Workplace City',
            'workplace_unspecifiedWorkplace' => 'Unspecified Workplace',
            'workplace_region' => 'Workplace Region',
            'workplace_municipality' => 'Workplace Municipality',
            'workplace_showMap' => 'Show Map',
            'languages_0_name' => 'Languages Name',
            'languages_0_required' => 'Languages Required',
            'workExperiences_0_name' => 'Work Experiences Name',
            'workExperiences_0_required' => 'Work Experiences Required',
            'contacts_0_name' => 'Contacts Name',
            'contacts_0_surname' => 'Contacts Surname',
            'contacts_0_position' => 'Contacts Position',
            'contacts_0_mobileNumber' => 'Contacts Mobile Number',
            'contacts_0_phoneNumber' => 'Contacts Phone Number',
            'contacts_0_email' => 'Contacts Email',
            'contacts_0_union' => 'Contacts Union',
            'contacts_0_description' => 'Contacts Description',
            'contacts_1_name' => 'Contacts Name',
            'contacts_1_surname' => 'Contacts Surname',
            'contacts_1_position' => 'Contacts Position',
            'contacts_1_mobileNumber' => 'Contacts Mobile Number',
            'contacts_1_phoneNumber' => 'Contacts Phone Number',
            'contacts_1_email' => 'Contacts Email',
            'contacts_1_union' => 'Contacts Union',
            'contacts_1_description' => 'Contacts Description',
            'skills_0_name' => 'Skills Name',
            'skills_0_required' => 'Skills Required',
            'skills_1_name' => 'Skills Name',
            'skills_1_required' => 'Skills Required',
            'skills_2_name' => 'Skills Name',
            'skills_2_required' => 'Skills Required',
        ]
    ];

    public $arrSettingStartMenuRegion = [
        'language_ukrainian' => [
            'title' => 'Виберіть регіон:'
        ],
        'language_russian'=>[
            'title'=>'Выберите регион:'
        ],
        'language_english'=>[
            'title'=>'Choose region:'
        ]
    ];

    public $arrSettingStartMenuSpecialist = [
        'language_ukrainian' => [
            'title' => 'Виберіть спеціальність:',
            'buttonTranslate'=>'Перекласти:'
        ],
        'language_russian'=>[
            'title'=>'Выберите специальность:',
            'buttonTranslate'=>'Перевести:'
        ],
        'language_english'=>[
            'title'=>'Select specialty:',
            'buttonTranslate'=>'Translate:'
        ]
    ];
    public $arrSettingStartMenu = [
        'language_ukrainian'=>[
            'title'=>'Зробіть вибір ресурсів:',
            'platsbankenButton' => 'Банк локацій:',
            'webbplatserButton'=>'Зовнішні сайти:'
        ],
        'language_russian'=>[
            'title'=>'Сделайте выбор ресурсов:',
            'platsbankenButton' => 'Банк локаций:',
            'webbplatserButton'=>'Внешние сайты:'
        ],
        'language_english' =>[
            'title'=>'Make a selection of resources:',
            'platsbankenButton' => 'Location bank',
            'webbplatserButton'=>'External sites:'
        ]
    ];
    public $arrSettingStartMenuOccupation = [
        'language_ukrainian' => [
            'title' => 'Виберіть напрямок:',
            'buttonTranslate'=>'Перекласти:'
        ],
        'language_russian'=>[
            'title'=>'Выберите направление:',
            'buttonTranslate'=>'Перевести:'
        ],
        'language_english'=>[
            'title'=>'Select direction:',
            'buttonTranslate'=>'Translate:'
        ]
    ];

    public $arrSettingStartMenuCity = [
        'language_ukrainian' => [
            'title' => 'Виберіть місто:'
        ],
        'language_russian'=>[
            'title'=>'Выберите город:'
        ],
        'language_english'=>[
            'title'=>'Select city:'
        ]
    ];
}