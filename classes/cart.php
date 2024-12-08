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

    // Haal de winkelmand op uit de database
    public function getCart() {
        // Query die producten ophaalt met de hoeveelheid uit de `user_product` tabel
        $stmt = $this->db->prepare(
            "SELECT *
            FROM user_product up
            JOIN products p ON p.id = up.product_id
            WHERE up.user_id = :user_id"
        );
        $stmt->bindParam(':user_id', $this->userId, PDO::PARAM_INT);
        $stmt->execute();
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Groepeer producten en tel de hoeveelheden bij elkaar op
        $groupedCartItems = [];
        foreach ($cartItems as $item) {
            $productId = $item['product_id'];
            if (isset($groupedCartItems[$productId])) {
                $groupedCartItems[$productId]['quantity'] += $item['quantity']; // Voeg de hoeveelheid samen
            } else {
                $groupedCartItems[$productId] = $item;
            }
        }
    
        return $groupedCartItems;
    }
    

    // Bereken de totale prijs van de winkelmand
    public function calculateTotal() {
        $total = 0;
        foreach ($this->getCart() as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    // Voeg een item toe aan de winkelmand of werk de hoeveelheid bij
    public function addItem($productId, $quantity = 1) {
        // Validate quantity
        if (!is_numeric($quantity) || $quantity <= 0) {
            echo "Invalid quantity.";
            return;
        }
    
        // Controleer of het product al in de winkelmand zit
        $stmt = $this->db->prepare("SELECT * FROM user_product WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->bindParam(':user_id', $this->userId, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($existingItem) {
            // Update de hoeveelheid als het product al bestaat
            $newQuantity = $existingItem['quantity'] + $quantity;
            $stmt = $this->db->prepare(
                "UPDATE user_product 
                SET quantity = :quantity 
                WHERE user_id = :user_id AND product_id = :product_id"
            );
            $stmt->bindParam(':quantity', $newQuantity, PDO::PARAM_INT);
        } else {
            // Voeg een nieuw product toe aan de winkelmand
            $stmt = $this->db->prepare(
                "INSERT INTO user_product (user_id, product_id, quantity) 
                VALUES (:user_id, :product_id, :quantity)"
            );
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        }
    
        // Bind de andere parameters en voer de query uit
        $stmt->bindParam(':user_id', $this->userId, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            echo "Item successfully added or updated.";
        } else {
            // Log or debug errors if the query fails
            echo "Error executing query.";
            print_r($stmt->errorInfo());
        }
    }
    
    
    
    // Verwijder een item uit de winkelmand
    public function removeItem($productId) {
        $stmt = $this->db->prepare(
            "DELETE FROM user_product 
            WHERE user_id = :user_id AND product_id = :product_id"
        );
        $stmt->bindParam(':user_id', $this->userId, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Update de hoeveelheid van een item
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
            // Verwijder het item als de hoeveelheid 0 of minder is
            $this->removeItem($productId);
        }
    }

    // In de Cart class
    public function deleteProductsByUser($userId) {
        $query = $this->db->prepare("DELETE FROM user_product WHERE user_id = :user_id");
        $query->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $query->execute();
    }

}



?>







