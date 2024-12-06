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

       
        $this->conn = new mysqli($servername, $username, $password, $database);

      
        if ($this->conn->connect_error) {
            die("Verbinding mislukt: " . $this->conn->connect_error);
        }
    }


    public function getConnection()
    {
        return $this->conn;
    }

   
    public function closeConnection()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

?>


