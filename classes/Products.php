<?php

class Product
{
    private $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function addProduct($title, $category, $description, $price, $imagePath)
    {
        $stmt = $this->db->prepare("INSERT INTO products (title, category, description, price, image, created_at) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssdss", $title, $category, $description, $price, $imagePath, $createdAt);

        return $stmt->execute();
    }

    public function updateProduct($id, $title, $category, $description, $price, $imagePath = null)
    {
        if (!empty($imagePath)) {
            $query = "UPDATE products SET title = ?, category = ?, description = ?, price = ?, image = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sssdsi", $title, $category, $description, $price, $imagePath, $id);
        } else {
            $query = "UPDATE products SET title = ?, category = ?, description = ?, price = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sssdi", $title, $category, $description, $price, $id);
        }

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
    public function getLatestProducts() {
        $query = "SELECT * FROM products ORDER BY created_at DESC LIMIT 5";
        return $this->db->query($query);
    }

    public function getProductsByCategory($category) {
        if ($category) {
            $query = "SELECT * FROM products WHERE category = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("s", $category);
            $stmt->execute();
            return $stmt->get_result();
        } else {
            $query = "SELECT * FROM products";
            return $this->db->query($query);
        }
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

        return null;
    }

public function searchProducts($query)
{
   
    $sql = "SELECT id, image, title, description, price FROM products WHERE title LIKE ? OR description LIKE ?";
    $stmt = $this->db->prepare($sql);
    $searchTerm = '%' . $query . '%'; 
    $stmt->bind_param('ss', $searchTerm, $searchTerm); 
    $stmt->execute();
    return $stmt->get_result();
}

}
?>

