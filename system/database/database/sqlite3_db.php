<?php

final class Sqlite3_db {
    private $connection;

    public function __construct($hostname, $username = null, $password = null, $database, $port = '3306', $charset = 'utf8mb4')
    {
        $this->connection = new SQLite3($hostname.$database, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, $password);

        if (!$this->connection) {
            throw new \Exception('Error: ' . $this->connection->lastErrorMsg()  . '<br />Error No: ' . $this->connection->lastErrorCode());
        }
    }

    public function query($sql){
        //$query = $this->connection->query($sql);

        if ($stm = $this->connection->prepare($sql)) {

            $query = $stm->execute();

            if (!$query instanceof \SQLite3Result || $query->numColumns() == 0)
                return true;


            $data = [];
            while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
                $data[] = $row;
            }
            $result = new \stdClass();
            $result->num_rows = (!empty($data)) ? count($data) : 0;
            $result->row = isset($data[0]) ? $data[0] : array();
            $result->rows = $data;
            $query->finalize();
            return $result;

        } else {
            throw new \Exception('Error: ' . $this->connection->lastErrorMsg()  . '<br />Error No: ' . $this->connection->lastErrorCode() . '<br />' . $sql);
        }

    }

    public function escape($value) {
        return $this->connection->escapeString($value);
    }

    public function countAffected() {
        return $this->connection->changes();
    }
    public function getLastId() {
        return $this->connection->lastInsertRowID();
    }

    public function isConnected() {
        return ($this->connection) ? true : false;
    }

    public function __destruct() {
        $this->connection->close();
    }
}