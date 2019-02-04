<?php
final class nullStatement {
    private $connection;

    public function __construct($hostname, $username, $password, $database, $charset = 'utf8mb4') {
        $this->connection = NULL;
    }

    public function query($sql) {
        $result = new \stdClass();
        $result->num_rows = NULL;
        $result->row = NULL;
        $result->rows = NULL;
        return $result;
    }

    public function escape($value) {
        return NULL;
    }

    public function countAffected() {
        return NULL;
    }

    public function getLastId() {
        return NULL;
    }

    public function __destruct() {
        return NULL;
    }
}
?>