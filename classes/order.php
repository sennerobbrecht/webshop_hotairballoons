<?php
class Order {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }


    public function placeOrder($email, $country, $city, $postalCode, $address, $houseNumber, $totalAmount) {
        $query = "INSERT INTO orders (email, country, city, postal_code, address, house_number, total_amount)
                  VALUES (:email, :country, :city, :postal_code, :address, :house_number, :total_amount)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':country', $country);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':postal_code', $postalCode);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':house_number', $houseNumber);
        $stmt->bindParam(':total_amount', $totalAmount);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function addOrderItem($orderId, $productId, $productName, $quantity, $price) {
        $query = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price) 
                  VALUES (:order_id, :product_id, :product_name, :quantity, :price)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':product_name', $productName);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':price', $price);
        $stmt->execute();
    }

    public function getOrderItemsByOrderId($orderId) {
        $query = "SELECT * FROM order_items WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            WHERE o.email = :email
            ORDER BY o.created_at DESC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
