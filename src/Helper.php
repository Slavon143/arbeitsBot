<?php

namespace src;

class Helper
{

    public static function truncateText($text, $length = 2000)
    {
        // Обрезаем текст до заданной длины
        $truncatedText = mb_substr($text, 0, $length);
        // Удаляем последнее слово, чтобы избежать обрыва слова
        $truncatedText = preg_replace('/\s+?(\S+)?$/', '', $truncatedText);
        return $truncatedText;
    }

    public static function flattenArray($array, $prefix = '')
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, self::flattenArray($value, $prefix . $key . '_'));
            } elseif (!empty($value)) {
                $result[$prefix . $key] = $value;
            }
        }
        return $result;
    }

    public static function renameKeys($array, $renameArray)
    {
        $result = [];
        foreach ($renameArray as $oldKey => $newKey) {
            if (isset($array[$oldKey])) {
                $result[$newKey] = $array[$oldKey];
            }
        }
        return $result;
    }

    public static function processJobData($ad, $newArray)
    {
        $flattenedArray = Helper::flattenArray($ad);
        $flattenedArray['description'] = Helper::truncateText(strip_tags(str_ireplace("\n", '', $flattenedArray['description'])));
        $rename = Helper::renameKeys($flattenedArray, $newArray);

        $str = '';
        foreach ($rename as $key => $item) {
            $str .= '<b>' . $key . '</b>' . ': ' . $item . "\n";
        }
        return $str;
    }

    public static function debug($data)
    {
        file_put_contents(__DIR__ . '/classDebug.txt', var_export($data, 1));
    }


    public static function specialistDataTranslate($arr, $occupation_id, $translete)
    {
        if (!empty($arr)) {
            $str = '';
            foreach ($arr as $value) {
                if ($value['id'] == $occupation_id) {
                    foreach ($value['items'] as $item) {
                        if (isset($item['name'], $item['id'])) {
                            $str .= $item['name'] . '>>>' . $item['id'] . '???';
                        }
                    }
                }
            }

            $strTranslate = $translete->translate($str);
            $strTranslate = strip_tags($strTranslate);
            if ($strTranslate) {
                $data = [];
                $transleteData = explode("???", $strTranslate);

                foreach ($transleteData as $item) {
                    $item = explode(">>>", $item);
                    if (isset($item[0], $item[1])) {
                        $data[] = [
                            'name' => $item[0],
                            'id' => $item[1]
                        ];
                    }
                }
                return $data;
            }
        }
    }


    public static function occupationDataTranslate($array, $translete)
    {
        // Проверяем наличие элементов в массиве
        if (empty($array)) {
            return [];
        }

        $str = '';
        foreach ($array as $key => $value) {
            // Проверяем наличие ожидаемых ключей в массиве
            if (isset($value['name'], $value['id'])) {
                $str .= $value['name'] . '&&' . $value['id'] . ">>>";
            }
        }

        // Переводим строку
        $transStr = $translete->translate($str);
        $transStr = explode(">>>", $transStr);

        $res = [];
        foreach ($transStr as $value) {
            // Разбиваем строку на части по символу '&&'
            $parts = explode("&&", $value);

            // Проверяем, что массив содержит обе части (name и id)
            if (isset($parts[0], $parts[1])) {
                $res[] = [
                    'name' => $parts[0],
                    'id' => $parts[1]
                ];
            }
        }
        return $res;
    }

}