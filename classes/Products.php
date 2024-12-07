<?php
class Product {
    private $conn;

    // Constructor accepts PDO object
    public function __construct(PDO $dbConnection) {
        $this->conn = $dbConnection;
    }

    public function getAllProducts() {
        $query = "SELECT * FROM products";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to search products based on a query
    public function searchProducts($query) {
        $query = "%" . $query . "%"; // Adding wildcard for LIKE operator
        $sql = "SELECT * FROM products WHERE title LIKE :query OR description LIKE :query";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':query', $query, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($id) {
        $query = "SELECT * FROM products WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addProduct($title, $category, $description, $price, $imagePath) {
        $query = "INSERT INTO products (title, category, description, price, image) 
                  VALUES (:title, :category, :description, :price, :image)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':category', $category, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt->bindParam(':image', $imagePath, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function updateProduct($id, $title, $category, $description, $price, $imagePath) {
        $query = "UPDATE products 
                  SET title = :title, category = :category, description = :description, 
                      price = :price, image = :image 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':category', $category, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt->bindParam(':image', $imagePath, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function deleteProduct($id) {
        $query = "DELETE FROM products WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getLatestProducts() {
        $query = "SELECT * FROM products ORDER BY created_at DESC LIMIT 5"; // or change based on your requirements
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    public function getProductsByCategory($category = '') {
        // If no category is selected, fetch all products
        if (empty($category)) {
            $query = "SELECT * FROM products";
        } else {
            // Fetch products based on the selected category
            $query = "SELECT * FROM products WHERE category = :category";
        }
        
        $stmt = $this->conn->prepare($query);
    
        if (!empty($category)) {
            $stmt->bindParam(':category', $category, PDO::PARAM_STR);
        }
    
        $stmt->execute();
        return $stmt;
    }
    
    
}
?>



