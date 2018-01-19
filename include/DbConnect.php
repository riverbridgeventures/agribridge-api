<?php 


class DbConnect {

    private $conn;

    function __construct(){ }

    function connect(){
        include_once dirname(__FILE__) . '/Config.php';
        $this->conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
        if (mysqli_connect_errno()) {
            echo "Failed to connect to Mysql: " . mysqli_connect_error();
        }
        return $this->conn;
    }

    function PDO(){
        include_once dirname(__FILE__) . '/Config.php';

        $host = DB_HOST;
        $db = DB_NAME;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO("mysql:host=$host;dbname=$db", DB_USERNAME, DB_PASSWORD, $options);

        $this->conn = $pdo;
        return $this->conn;
    }
}