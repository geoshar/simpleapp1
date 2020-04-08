<?php

class MySQLii {
    private $connection;
    private $connected;

    public function __construct($hostname, $username, $password, $database, $port = '3306') {
        try {
            mysqli_report(MYSQLI_REPORT_STRICT);

            $this->connection = @new \mysqli($hostname, $username, $password, $database, $port);
        } catch (\mysqli_sql_exception $e) {
            throw new \Exception('Error: Could not make a database link using ' . $username . '@' . $hostname . '!');
        }

        $this->connection->set_charset("utf8");
        $this->connection->query("SET SQL_MODE = ''");
    }

    public function query($sql, $count = false) {
        $query = $this->connection->query($sql);

        $count = ($count) ? $this->connection->query('SELECT FOUND_ROWS()')->fetch_row()[0] : 0;

        if (!$this->connection->errno) {
            if ($query instanceof \mysqli_result) {
                $data = array();

                while ($row = $query->fetch_assoc()) {
                    $data[] = $row;
                }

                $result = new \stdClass();
                $result->num_rows = $query->num_rows;
                $result->row = isset($data[0]) ? $data[0] : array();
                $result->rows = $data;
                $result->count = ($count) ? $count : 0;

                $query->close();

                return $result;
            } else {
                return true;
            }
        } else {
            throw new \Exception('Error: ' . $this->connection->error  . '<br />Error No: ' . $this->connection->errno . '<br />' . $sql);
        }
    }
}