<?php

class Order {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

   
    public function placeOrder($email, $country, $city, $postalCode, $address, $houseNumber, $totalAmount) {
        $query = "INSERT INTO orders (email, country, city, postal_code, address, house_number, total_amount)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssssd", $email, $country, $city, $postalCode, $address, $houseNumber, $totalAmount);
        $stmt->execute();
        return $this->conn->insert_id;  
    }


    public function addOrderItem($orderId, $productId, $productName, $quantity, $price) {
        $query = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price)
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iisid", $orderId, $productId, $productName, $quantity, $price);
        $stmt->execute();
    }

  
    public function getOrderItemsByOrderId($orderId) {
        $query = "SELECT * FROM order_items WHERE order_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = $result->fetch_all(MYSQLI_ASSOC);
        return $items;
    }

 
    public function getOrdersByEmail($email) {
        $query = "
            SELECT 
                o.id AS order_id, 
                oi.product_name, 
                oi.quantity, 
                oi.price, 
                o.total_amount, 
                o.created_at
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            WHERE o.email = ?
            ORDER BY o.created_at DESC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
        
        return $orders;
    }

    public function getOrderedProducts($userId) {
        $stmt = $this->db->getConnection()->prepare('SELECT * FROM orders WHERE user_id = :user_id');
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function hasPurchasedProduct($userId, $productId) {
        $stmt = $this->db->getConnection()->prepare('SELECT 1 FROM orders WHERE user_id = :user_id AND product_id = :product_id');
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        return $stmt->fetchColumn() !== false;
    }
    public function getAllOrdersWithItems() {
        $query = "
            SELECT 
                o.id AS order_id,
                o.email,
                oi.product_name,
                oi.price,
                oi.quantity,
                o.total_amount,
                o.created_at
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            ORDER BY o.created_at DESC
        ";
        
        $stmt = $this->conn->query($query);
        $orders = [];
        if ($stmt->num_rows > 0) {
            while ($row = $stmt->fetch_assoc()) {
                $orders[] = $row;
            }
        }
        return $orders;
    }
    
    public static function renderOrdersTable($orders) {
        echo '<table>';
        echo '<thead>
                <tr>
                    <th>Email</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price (per item)</th>
                    <th>Total Amount</th>
                    <th>Created At</th>
                </tr>
              </thead>';
        echo '<tbody>';

        if (count($orders) > 0) {
            foreach ($orders as $order) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($order['email']) . '</td>';
                echo '<td>' . htmlspecialchars($order['product_name']) . '</td>';
                echo '<td>' . htmlspecialchars($order['quantity']) . '</td>';
                echo '<td>' . htmlspecialchars(number_format($order['price'], 2)) . ' €</td>';
                echo '<td>' . htmlspecialchars(number_format($order['total_amount'], 2)) . ' €</td>';
                echo '<td>' . htmlspecialchars($order['created_at']) . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6">Geen orders gevonden.</td></tr>';
        }

        echo '</tbody>';
        echo '</table>';
    }

    
}

?>
