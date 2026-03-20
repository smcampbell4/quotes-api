<?php
class Database {

    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;

    public $conn;

    public function __construct() {
        $this->host = getenv('DB_HOST');
        $this->db_name = getenv('DB_NAME');
        $this->username = getenv('DB_USER');
        $this->password = getenv('DB_PASS');
        $this->port = getenv('DB_PORT') ?: '5432';
    }

    public function connect() {

        $this->conn = null;

        try {
            $this->conn = new PDO(
                "pgsql:host=" . $this->host .
                ";port=" . $this->port .
                ";dbname=" . $this->db_name .
                ";sslmode=require",
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>