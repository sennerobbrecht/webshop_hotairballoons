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
          
            $dsn = "mysql:host=$servername;dbname=$database;charset=utf8";
            $this->conn = new PDO($dsn, $username, $password);

         
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
      
        $this->conn = null;
    }
}

?>



