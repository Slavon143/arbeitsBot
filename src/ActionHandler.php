<?php

namespace src;
class ActionHandler {
    private $historyDirPath;

    public function __construct($historyDirPath) {
        $this->historyDirPath = $historyDirPath;
    }

    // Метод для добавления действия в историю для указанного chat_id
    public function addToHistory($chatId, $action) {
        $historyFilePath = $this->getHistoryFilePath($chatId);
        $history = json_decode(file_get_contents($historyFilePath), true);
        $history[] = $action;
        file_put_contents($historyFilePath, json_encode($history));
    }

    // Метод для получения предыдущего действия из истории для указанного chat_id
    public function getPreviousAction($chatId) {
        $historyFilePath = $this->getHistoryFilePath($chatId);
        $history = json_decode(file_get_contents($historyFilePath), true);

        // Получаем историю без последнего элемента
        $previousHistory = array_slice($history, 0, -1);

        // Получаем последний элемент в новой истории
        return end($previousHistory);
    }

    // Метод для удаления history файла для указанного chat_id
    public function removeHistoryFile($chatId) {
        $historyFilePath = $this->getHistoryFilePath($chatId);
        if (file_exists($historyFilePath)) {
            unlink($historyFilePath);
        }
    }

    public function removeLastAction($chatId) {
        $historyFilePath = $this->getHistoryFilePath($chatId);
        $history = json_decode(file_get_contents($historyFilePath), true);

        // Удаляем последний элемент истории
        array_pop($history);

        // Записываем обновленную историю в файл
        file_put_contents($historyFilePath, json_encode($history));
    }

    // Вспомогательный метод для получения пути к файлу истории для указанного chat_id
    private function getHistoryFilePath($chatId) {
        return $this->historyDirPath . '/' . $chatId . '_history.json';
    }
}
