<?php

class Database
{
    private $conn;

    public function __construct()
    {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "webshop_hotairballoons";

        try {
            // DSN (Data Source Name) format for PDO
            $dsn = "mysql:host=$servername;dbname=$database;charset=utf8";
            $this->conn = new PDO($dsn, $username, $password);

            // Set PDO to throw exceptions on errors
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Verbinding mislukt: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function closeConnection()
    {
        // Closing the connection is not necessary in PDO; the connection will be closed automatically when the object is destroyed
        $this->conn = null;
    }
}

?>



