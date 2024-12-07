<?php

class Category
{
    private $db;

    public function __construct($database)
    {
        // Get the PDO connection from the database class
        $this->db = $database->getConnection();
    }

    public function getProductsByCategory($category)
    {
        // Prepare the query using PDO
        $stmt = $this->db->prepare("SELECT id, image, title, description, price FROM products WHERE category = :category");
        
        // Bind the parameter using bindValue() for PDO
        $stmt->bindValue(':category', $category, PDO::PARAM_STR);

        // Execute the statement
        $stmt->execute();

        // Fetch the results
        $producten = [];

        // Check if any results are returned
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $producten[] = [
                    'id' => $row['id'],
                    'afbeelding' => $row['image'],
                    'titel' => $row['title'],
                    'beschrijving' => $row['description'],
                    'prijs' => '€' . number_format($row['price'], 2, ',', '.')
                ];
            }
        }

        return $producten;
    }
}

?>



