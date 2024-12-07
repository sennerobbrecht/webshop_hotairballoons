<?php
session_start();

// Include the necessary files
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Products.php';

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit();
}

// Create a Database object and establish a connection
$db = new Database();
$conn = $db->getConnection();

// Create a Product object to interact with the product table
$product = new Product($conn);

// Get product ID from the URL parameter and validate it
$product_id = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 0;

// Fetch product details by ID
$productDetails = $product->getProductById($product_id);

if (!$productDetails) {
    echo "Product niet gevonden.";
    exit();
}
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
// Display the appropriate navbar based on the user
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
        <form onsubmit="event.preventDefault(); addToCart(<?php echo $productDetails['id']; ?>, '<?php echo addslashes($productDetails['title']); ?>', <?php echo $productDetails['price']; ?>, '<?php echo addslashes($productDetails['image']); ?>');">
            <button type="submit" class="add-to-cart-btn">Toevoegen aan winkelwagentje</button>
        </form>
    </div>
</div>

<div id="notification-popup" class="hidden"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="javascript/product.js"></script>

</body>
</html>












