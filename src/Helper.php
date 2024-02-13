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

    public static function processJobData($ad, $newArray) {
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

}