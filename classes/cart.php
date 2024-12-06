<?php

class Cart {
    
    public function getCart() {
        return $_SESSION['cart'] ?? [];
    }

  
    public function calculateTotal() {
        $total = 0;
        foreach ($this->getCart() as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

 
    public function removeItem($productId) {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
    }
}
?>

