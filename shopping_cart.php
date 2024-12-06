<?php
session_start();
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Cart.php';
require_once __DIR__ . '/classes/Order.php';


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

$email = $_SESSION['email'] ?? '';
$balance = $_SESSION['balance'] ?? 1000;

$database = new Database();
$db = $database->getConnection();
$cart = new Cart();

if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $productId = intval($_GET['remove']);
    $cart->removeItem($productId);
    header('Location: shopping_cart.php');
    exit();
}



$totalAmount = $cart->calculateTotal();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
    if ($totalAmount > $balance) {
        
        echo '<script>showInsufficientBalancePopup();</script>';
    } else {
       
        $_SESSION['balance'] = $balance - $totalAmount;  

      
        $orderEmail = $_POST['email'];
        $country = $_POST['country'];
        $city = $_POST['city'];
        $postalCode = $_POST['postalCode'];
        $address = $_POST['address'];
        $houseNumber = $_POST['houseNumber'];

       
        $order = new Order($db);
        $orderId = $order->placeOrder($orderEmail, $country, $city, $postalCode, $address, $houseNumber, $totalAmount);

       
        foreach ($cart->getCart() as $productId => $item) {
            $order->addOrderItem($orderId, $productId, $item['title'], $item['quantity'], $item['price']);
        }

       
        $_SESSION['cart'] = [];

     
        echo '<script>alert("Bestelling is geplaatst!"); window.location.href="shopping_cart.php";</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Winkelmand</title>
    <link rel="stylesheet" href="css/shoppingcart.css">
</head>
<body>
<?php include_once 'navbar.php'; ?>

<div class="order-container">
    <h1>Winkelmand</h1>
    <?php if (!empty($cart->getCart())): ?>
        <?php foreach ($cart->getCart() as $productId => $item): ?>
            <div class="product-card">
                <img src="<?php echo htmlspecialchars($item['image'] ?? 'placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($item['title'] ?? 'Onbekend product'); ?>">
                <div class="product-info">
                    <h2 class="product-title"><?php echo htmlspecialchars($item['title'] ?? 'Onbekend product'); ?></h2>
                    <p class="product-price" data-price="<?php echo $item['price']; ?>">€<?php echo number_format($item['price'], 2, ',', '.'); ?></p>
                    <form class="quantity-controls">
                        <input type="hidden" name="product_id" value="<?php echo $productId; ?>" data-product-id="<?php echo $productId; ?>">
                        <button type="button" onclick="changeQuantity(this, -1)">-</button>
                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" readonly>
                        <button type="button" onclick="changeQuantity(this, 1)">+</button>
                    </form>
                </div>
                <a href="shopping_cart.php?remove=<?php echo $productId; ?>" class="remove-btn">Verwijderen</a>
            </div>
        <?php endforeach; ?>
        <div class="total-amount">
    Totaal: €<?php echo number_format($totalAmount, 2, ',', '.'); ?>
</div>


<div id="userBalance" style="display:none;">€<?php echo number_format($balance, 2, ',', '.'); ?></div>

        <button id="showOrderForm" class="order-button" onclick="return placeOrder(event);">Bestelling Plaatsen</button>

    <?php else: ?>
        <p>Je winkelmand is leeg.</p>
    <?php endif; ?>
</div>


<div id="orderForm" class="popup">
    <div class="popup-content">
        <h2>Bestelling Plaatsen</h2>
        <form method="POST" class="checkout-form">
            <input type="email" name="email" value="<?php echo $email; ?>" required>
            <input type="text" name="country" placeholder="Land" required>
            <input type="text" name="city" placeholder="Stad" required>
            <input type="text" name="postalCode" placeholder="Postcode" required>
            <input type="text" name="address" placeholder="Adres" required>
            <input type="text" name="houseNumber" placeholder="Huisnummer" required>
            <button type="submit" name="submit_order">Bestelling Bevestigen</button>
        </form>
        <button onclick="closePopup()">Annuleren</button>
    </div>
</div>


<div id="insufficientBalancePopup" class="popup" style="display:none;">
    <div class="popup-content">
        <h2>Onvoldoende saldo</h2>
        <p>Je saldo is niet hoog genoeg om deze bestelling te plaatsen.</p>
        <button onclick="closeInsufficientBalancePopup()">Sluiten</button>
    </div>
</div>


<script src="javascript/shoppingcart.js"></script>

<div id="userBalance" style="display:none;"><?php echo $balance; ?></div>
<div id="totalAmount" style="display:none;"><?php echo $totalAmount; ?></div>

</body>
</html>










