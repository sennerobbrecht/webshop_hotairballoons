<?php

class Category
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database->getConnection();
    }

    public function getProductsByCategory($category)
    {
        
        $stmt = $this->db->prepare("SELECT id, image, title, description, price FROM products WHERE category = ?");
        $stmt->bind_param("s", $category);
        $stmt->execute();

        $result = $stmt->get_result();
        $producten = [];

       
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
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


