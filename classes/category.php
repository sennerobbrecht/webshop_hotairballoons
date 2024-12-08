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
      
        $stmt = $this->db->prepare("SELECT id, image, title, description, price FROM products WHERE category = :category");
        
      
        $stmt->bindValue(':category', $category, PDO::PARAM_STR);

        $stmt->execute();

        $producten = [];

       
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



