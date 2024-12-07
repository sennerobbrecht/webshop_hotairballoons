<?php
session_start();
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Cart.php';
require_once __DIR__ . '/classes/Order.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

// Database connectie
$database = new Database();
$db = $database->getConnection();

// Gebruikersgegevens ophalen
$email = $_SESSION['email'] ?? '';
$query = $db->prepare("SELECT balance FROM users WHERE email = ?");
$query->bind_param("s", $email); // Bind de parameter (string)
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

$balance = $user['balance'] ?? 0; // Default saldo is 0 indien niet aanwezig

// Winkelwagen
$cart = new Cart();
$totalAmount = $cart->calculateTotal(); // Bereken het totale bedrag van de winkelwagen

// Product verwijderen uit winkelwagen
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $productId = intval($_GET['remove']);
    $cart->removeItem($productId);
    header('Location: shopping_cart.php');
    exit();
}

// Bestelling plaatsen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
    if ($totalAmount > $balance) {
        echo '<script>alert("Onvoldoende saldo om de bestelling te plaatsen.");</script>';
    } else {
        // Bestelgegevens ophalen
        $orderEmail = $_POST['email'];
        $country = $_POST['country'];
        $city = $_POST['city'];
        $postalCode = $_POST['postalCode'];
        $address = $_POST['address'];
        $houseNumber = $_POST['houseNumber'];

        // Nieuwe bestelling aanmaken
        $order = new Order($db);
        $orderId = $order->placeOrder($orderEmail, $country, $city, $postalCode, $address, $houseNumber, $totalAmount);

        // Voeg producten toe aan de bestelling
        foreach ($cart->getCart() as $productId => $item) {
            $order->addOrderItem($orderId, $productId, $item['title'], $item['quantity'], $item['price']);
        }

        // Saldo berekenen en updaten
        $newBalance = $balance - $totalAmount;
        $updateBalance = $db->prepare("UPDATE users SET balance = ? WHERE email = ?");
        $updateBalance->bind_param("ds", $newBalance, $email); // d = double, s = string
        $updateBalance->execute();

        // Winkelwagen legen
        $_SESSION['cart'] = [];
        $_SESSION['balance'] = $newBalance;

        echo '<script>alert("Bestelling succesvol geplaatst!"); window.location.href="shopping_cart.php";</script>';
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
                    <h2 class="product-title"><?php echo htmlspecialchars($item['title']); ?></h2>
                    <p class="product-price">€<?php echo number_format($item['price'], 2, ',', '.'); ?></p>
                    <form class="quantity-controls">
                        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
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
        <div id="userBalance" style="display:none;"><?php echo number_format($balance, 2, ',', '.'); ?></div>
        <button id="showOrderForm" class="order-button">Bestelling Plaatsen</button>
    <?php else: ?>
        <p>Je winkelmand is leeg.</p>
    <?php endif; ?>
</div>

<div id="orderForm" class="popup" style="display:none;">
    <div class="popup-content">
        <h2>Bestelling Plaatsen</h2>
        <form method="POST">
            <input type="email" name="email" value="<?php echo $email; ?>" readonly>
            <input type="text" name="country" placeholder="Land" required>
            <input type="text" name="city" placeholder="Stad" required>
            <input type="text" name="postalCode" placeholder="Postcode" required>
            <input type="text" name="address" placeholder="Adres" required>
            <input type="text" name="houseNumber" placeholder="Huisnummer" required>
            <button type="submit" name="submit_order">Bevestigen</button>
        </form>
        <button onclick="closePopup()">Annuleren</button>
    </div>
</div>

<div id="insufficientBalancePopup" class="popup" style="display:none;">
    <div class="popup-content">
        <h2>Onvoldoende saldo</h2>
        <p>Je saldo is niet hoog genoeg om deze bestelling te plaatsen.</p>
        <button onclick="closePopup()">Sluiten</button>
    </div>
</div>

<script>
    document.getElementById('showOrderForm').addEventListener('click', function () {
        const totalAmount = parseFloat(document.querySelector('.total-amount').textContent.replace('€', '').replace(',', '.'));
        const balance = parseFloat(document.getElementById('userBalance').textContent.replace('€', '').replace(',', '.'));

        if (totalAmount > balance) {
            document.getElementById('insufficientBalancePopup').style.display = 'flex';
        } else {
            document.getElementById('orderForm').style.display = 'flex';
        }
    });

    function closePopup() {
        document.getElementById('insufficientBalancePopup').style.display = 'none';
        document.getElementById('orderForm').style.display = 'none';
    }
</script>
</body>
</html>













