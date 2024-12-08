<?php

class Cart {
    private $db;
    private $userEmail;
    private $userId;

    public function __construct($db, $userEmail, $userId) {
        $this->db = $db;
        $this->userEmail = $userEmail;
        $this->userId = $userId;
    }

   
    public function getCart() {
       
        $stmt = $this->db->prepare(
            "SELECT *
            FROM user_product up
            JOIN products p ON p.id = up.product_id
            WHERE up.user_id = :user_id"
        );
        $stmt->bindParam(':user_id', $this->userId, PDO::PARAM_INT);
        $stmt->execute();
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $groupedCartItems = [];
        foreach ($cartItems as $item) {
            $productId = $item['product_id'];
            if (isset($groupedCartItems[$productId])) {
                $groupedCartItems[$productId]['quantity'] += $item['quantity']; 
            } else {
                $groupedCartItems[$productId] = $item;
            }
        }
    
        return $groupedCartItems;
    }
    

    
    public function calculateTotal() {
        $total = 0;
        foreach ($this->getCart() as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

  
    public function addItem($productId, $quantity = 1) {
      
        if (!is_numeric($quantity) || $quantity <= 0) {
            echo "Invalid quantity.";
            return;
        }
    
        
        $stmt = $this->db->prepare("SELECT * FROM user_product WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->bindParam(':user_id', $this->userId, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($existingItem) {
          
            
            $newQuantity = $existingItem['quantity'] + $quantity;
            $stmt = $this->db->prepare(
                "UPDATE user_product 
                SET quantity = :quantity 
                WHERE user_id = :user_id AND product_id = :product_id"
            );
            $stmt->bindParam(':quantity', $newQuantity, PDO::PARAM_INT);
        } else {
         
            $stmt = $this->db->prepare(
                "INSERT INTO user_product (user_id, product_id, quantity) 
                VALUES (:user_id, :product_id, :quantity)"
            );
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        }
    
   
        $stmt->bindParam(':user_id', $this->userId, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            echo "Item successfully added or updated.";
        } else {
         
            echo "Error executing query.";
            print_r($stmt->errorInfo());
        }
    }
    
    
    
   
    public function removeItem($productId) {
        $stmt = $this->db->prepare(
            "DELETE FROM user_product 
            WHERE user_id = :user_id AND product_id = :product_id"
        );
        $stmt->bindParam(':user_id', $this->userId, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
    }

   
    public function updateItemQuantity($productId, $quantity) {
        if ($quantity > 0) {
            $stmt = $this->db->prepare(
                "UPDATE user_product 
                SET quantity = :quantity 
                WHERE user_id = :user_id AND product_id = :product_id"
            );
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $this->userId, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            
            $this->removeItem($productId);
        }
    }

  
    public function deleteProductsByUser($userId) {
        $query = $this->db->prepare("DELETE FROM user_product WHERE user_id = :user_id");
        $query->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $query->execute();
    }

}



?>







