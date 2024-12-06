<?php
session_start();


require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Products.php';

$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit();
}


$db = new Database();
$conn = $db->getConnection();


$product = new Product($db);


$product_id = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 0;


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










