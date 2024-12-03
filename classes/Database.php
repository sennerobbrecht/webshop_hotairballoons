<?php

class Database
{
    private $conn;

    public function __construct()
    {
        $servername = "localhost";
        $username = "root";
        $password = ""; // Je database-wachtwoord
        $database = "webshop_hotairballoons";

        // Maak verbinding
        $this->conn = new mysqli($servername, $username, $password, $database);

        // Controleer de verbinding
        if ($this->conn->connect_error) {
            die("Verbinding mislukt: " . $this->conn->connect_error);
        }
    }

    // Verkrijg de databaseverbinding
    public function getConnection()
    {
        return $this->conn;
    }

    // Sluit de databaseverbinding
    public function closeConnection()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

?>


