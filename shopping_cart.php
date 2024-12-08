<?php
session_start();
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/cart.php';
require_once __DIR__ . '/classes/order.php';
require_once __DIR__ . '/classes/User.php';

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

// Verbinding maken met de database
$database = new Database();
$db = $database->getConnection();

// Haal het saldo van de gebruiker op
$email = $_SESSION['email'] ?? '';
$query = $db->prepare("SELECT balance FROM users WHERE email = :email");
$query->bindParam(':email', $email, PDO::PARAM_STR);
$query->execute();
$user = $query->fetch(PDO::FETCH_ASSOC);
$balance = $user['balance'] ?? 0;

// Haal de userId op via de User class en het emailadres
$userClass = new User($db);
$userId = $userClass->getUserIdByEmail($email);

// Haal de winkelmand op
$cart = new Cart($db, $email, $userId);
$totalAmount = $cart->calculateTotal();

// Verwijder een item uit de winkelmand
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $productId = intval($_GET['remove']);
    $cart->removeItem($productId);
    $_SESSION['cart'] = $cart->getCart();
    header('Location: shopping_cart.php');
    exit();
}

// Voeg product toe aan de winkelmand
if (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
    $productId = $_GET['product_id'];

    // Haal productinformatie op uit de database
    $query = $db->prepare("SELECT id, title, price FROM products WHERE id = :product_id");
    $query->bindParam(':product_id', $productId, PDO::PARAM_INT);
    $query->execute();
    $product = $query->fetch(PDO::FETCH_ASSOC);

    // Voeg het product toe aan de winkelmand als het bestaat
    if ($product) {
        $stmt = $db->prepare("INSERT INTO user_product (user_id, product_id) VALUES (:user_id, :product_id)");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();

        $cart->addItem($productId, $product['title'], $product['price']);
    }

    header('Location: shopping_cart.php');
    exit();
}

// Bestelling plaatsen
if (isset($_POST['submit_order'])) {
    // Verkrijg de gegevens van het formulier
    $country = $_POST['country'] ?? '';
    $city = $_POST['city'] ?? '';
    $postalCode = $_POST['postalCode'] ?? '';
    $address = $_POST['address'] ?? '';
    $houseNumber = $_POST['houseNumber'] ?? '';

    // Controleer of het saldo voldoende is
    if ($totalAmount > $balance) {
        echo '<script>alert("Je hebt niet genoeg saldo om deze bestelling te plaatsen.");</script>';
    } else {
        // Plaats de bestelling in de database
        $orderClass = new Order($db);
        $orderId = $orderClass->placeOrder($email, $country, $city, $postalCode, $address, $houseNumber, $totalAmount);

        // Voeg de producten toe aan de bestelling
        foreach ($cart->getCart() as $productId => $item) {
            $orderClass->addOrderItem($orderId, $productId, $item['title'], $item['quantity'], $item['price']);
        }

        // Werk het saldo bij na de bestelling
        $newBalance = $balance - $totalAmount;
        $updateQuery = "UPDATE users SET balance = :newBalance WHERE email = :email";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(':newBalance', $newBalance, PDO::PARAM_STR);
        $updateStmt->bindParam(':email', $email, PDO::PARAM_STR);
        $updateStmt->execute();

        // Verwijder de producten uit de winkelmand van de gebruiker
        $cart->deleteProductsByUser($userId);

        // Reset de winkelmand
        $_SESSION['cart'] = [];
        header('Location: index.php'); // Verwijs naar bevestigingspagina
        exit();
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
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title'] ?? 'Onbekend product'); ?>">
                    <div class="product-info">
                        <h2 class="product-title"><?php echo htmlspecialchars($item['title']); ?></h2>
                        <p class="product-price" data-price="<?php echo $item['price']; ?>">€<?php echo number_format($item['price'], 2, ',', '.'); ?></p>
                        <form class="quantity-controls" method="POST" action="update_cart.php">
                            <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                            <button type="button" onclick="changeQuantity(this, -1)">-</button>
                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" id="quantity_<?php echo $productId; ?>" readonly>
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

    <script src="javascript/shoppingcart.js"></script>
</body>
</html>


