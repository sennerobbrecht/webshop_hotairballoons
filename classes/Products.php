<?php

class Product
{
    private $db;

    public function __construct(Database $database)
    {
        // Verkrijg de mysqli-verbinding via de getConnection() methode
        $this->db = $database->getConnection();
    }

    public function addProduct($title, $category, $description, $price, $imagePath)
    {
        $stmt = $this->db->prepare("INSERT INTO products (title, category, description, price, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssd", $title, $category, $description, $price, $imagePath);
        return $stmt->execute();
    }

    public function updateProduct($id, $title, $category, $description, $price, $imagePath)
    {
        $stmt = $this->db->prepare("UPDATE products SET title = ?, category = ?, description = ?, price = ?, image = ? WHERE id = ?");
        $stmt->bind_param("ssssdi", $title, $category, $description, $price, $imagePath, $id);
        return $stmt->execute();
    }

    public function deleteProduct($id)
    {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getAllProducts()
    {
        $result = $this->db->query("SELECT * FROM products");
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    public function getLatestProducts($limit = 5)
    {
        $query = "SELECT * FROM products ORDER BY id DESC LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    public function getProductsByCategory($category = '')
    {
        if ($category) {
            $query = "SELECT * FROM products WHERE category = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("s", $category);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $query = "SELECT * FROM products";
            $result = $this->db->query($query);
        }
        return $result;
    }
    public function getProductById($id)
{
    $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    return null; // Als het product niet bestaat, return null
}
public function searchProducts($query)
{
    // Zoek naar producten in de database die overeenkomen met de zoekterm
    $sql = "SELECT id, image, title, description, price FROM products WHERE title LIKE ? OR description LIKE ?";
    $stmt = $this->db->prepare($sql);
    $searchTerm = '%' . $query . '%'; // Voeg wildcards toe aan de zoekterm
    $stmt->bind_param('ss', $searchTerm, $searchTerm); // Bind de zoekterm aan de query
    $stmt->execute();
    return $stmt->get_result(); // Retourneer de zoekresultaten
}

}
?>

