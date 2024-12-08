<?php
session_start();

require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Products.php';
require_once __DIR__ . '/classes/User.php';

$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit();
}


$db = new Database();
$conn = $db->getConnection();  


$userClass = new User($conn);


$userId = $userClass->getUserIdByEmail($email);

$product = new Product($conn);

$product_id = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 0;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
        
       
        $quantity = 1;

      
        $query = "INSERT INTO user_product (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
           
            echo "<script>alert('Product toegevoegd aan winkelwagentje.');</script>";
        } else {
            echo "<script>alert('Er is een fout opgetreden.');</script>";
        }
    }
}




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
        <form method="POST" action="product.php">
            <input type="hidden" name="product_id" value="<?php echo $productDetails['id']; ?>">
            <button type="submit" class="add-to-cart-btn">Toevoegen aan winkelwagentje</button>
        </form>
    </div>
</div>

<div id="notification-popup" class="hidden"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="javascript/product.js"></script>

</body>
</html>














