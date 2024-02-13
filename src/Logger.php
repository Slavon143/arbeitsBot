<?php
namespace src;
class Logger {
    private $log_file;

    public function __construct($log_file) {
        $this->log_file = $log_file;
    }

    public function log($message) {
        // Открываем файл лога для добавления новой записи
        $file = fopen($this->log_file, 'a');

        if ($file) {
            // Форматируем сообщение для лога с указанием времени
            $log_message = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;

            // Записываем сообщение в файл
            fwrite($file, $log_message);

            // Закрываем файл
            fclose($file);

            return true;
        } else {
            // Если не удалось открыть файл, возвращаем false
            return false;
        }
    }
}

// Пример использования
$log_file = 'app.log'; // Путь к файлу лога
$logger = new Logger($log_file);
$logger->log('Сообщение для лога');
