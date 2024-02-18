<?php

namespace src;
class ActionHandler {
    private $db;

    public function __construct($dbFilePath) {
        $this->db = new \SQLite3($dbFilePath);

        // Создаем таблицу для хранения истории, если она не существует
        $this->db->exec('CREATE TABLE IF NOT EXISTS history (
                            chat_id TEXT,
                            action TEXT,
                            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
                        )');
    }

    public function addToHistory($chatId, $action) {
        $action = json_encode($action);
        $stmt = $this->db->prepare('INSERT INTO history (chat_id, action) VALUES (:chatId, :action)');
        $stmt->bindValue(':chatId', $chatId, SQLITE3_TEXT);
        $stmt->bindValue(':action', $action, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function getPreviousAction($chatId) {
        $stmt = $this->db->prepare('SELECT action FROM history WHERE chat_id = :chatId ORDER BY timestamp DESC LIMIT 1 OFFSET 1');
        $stmt->bindValue(':chatId', $chatId, SQLITE3_TEXT);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return ($row !== false) ? json_decode($row['action'],1) : null;
    }

    public function removeHistoryFile($chatId) {
        $stmt = $this->db->prepare('DELETE FROM history WHERE chat_id = :chatId');
        $stmt->bindValue(':chatId', $chatId, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function removeLastAction($chatId) {
        $this->db->exec('DELETE FROM history WHERE ROWID = (SELECT MAX(ROWID) FROM history WHERE chat_id = "'.$chatId.'")');
    }

    // Метод закрытия соединения с базой данных
    public function close() {
        $this->db->close();
    }
}

