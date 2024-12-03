<?php
session_start();

// Laad de benodigde klassen
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Products.php';

// Controleer of de gebruiker is ingelogd
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit();
}

// Maak de databaseverbinding
$db = new Database();
$conn = $db->getConnection(); // Verkrijg de mysqli verbinding

// Maak een object van de Product klasse
$product = new Product($db);

// Haal het product-ID op uit de URL
$product_id = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 0;

// Haal het product op uit de database
$productDetails = $product->getProductById($product_id);

if (!$productDetails) {
    echo "Product niet gevonden.";
    exit();
}

// Debugging: Controleer de opgehaalde productdetails
// var_dump($productDetails);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($productDetails['title']); ?></title>
    <link rel="stylesheet" href="css/product.css">
</head>
<body>
<?php
    // Controleer of de ingelogde gebruiker de admin is
    if ($email === 'admin@admin.com') {
        include_once 'admin-navbar.php';
    } else {
        include_once 'navbar.php';
    }
?>

<div class="product-page">
    <div class="product-image">
        <img src="<?php echo htmlspecialchars($productDetails['image']); ?>" alt="<?php echo htmlspecialchars($productDetails['title']); ?>">
    </div>
    <div class="product-details">
        <h1 class="product-title"><?php echo htmlspecialchars($productDetails['title']); ?></h1>
        <p class="product-description"><?php echo htmlspecialchars($productDetails['description']); ?></p>
        <p class="product-price"><?php echo '€' . htmlspecialchars($productDetails['price']); ?></p>
        <button class="add-to-cart-btn">Toevoegen aan winkelwagentje</button>
    </div>
</div>

</body>
</html>








