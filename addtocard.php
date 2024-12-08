<?php
session_start();

// Include necessary files
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/User.php';
require_once __DIR__ . '/classes/Products.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

// Get user email from session
$email = filter_var($_SESSION['email'], FILTER_SANITIZE_EMAIL);

// Validate the product ID from the POST data
$product_id = isset($_POST['product_id']) && is_numeric($_POST['product_id']) ? intval($_POST['product_id']) : 0;

if ($product_id <= 0) {
    echo "Invalid product ID.";
    exit();
}

// Initialize database connection and user instance
try {
    $db = new Database();
    $conn = $db->getConnection();
    $user = new User($conn);

    // Get the user ID based on the email
    $userId = $user->getUserIdByEmail($email);

    if (!$userId) {
        echo "User not found.";
        exit();
    }

    // Count the existing rows for this user and product combination
    $queryCount = "SELECT COUNT(*) as count, quantity FROM user_product WHERE user_id = :user_id AND product_id = :product_id";
    $stmtCount = $conn->prepare($queryCount);
    $stmtCount->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtCount->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmtCount->execute();
    $result = $stmtCount->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        // If the product exists, update the quantity by incrementing it
        $newQuantity = $result['quantity'] + 1;
        $queryUpdate = "UPDATE user_product SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id";
        $stmtUpdate = $conn->prepare($queryUpdate);
        $stmtUpdate->bindParam(':quantity', $newQuantity, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':product_id', $product_id, PDO::PARAM_INT);

        if ($stmtUpdate->execute()) {
            echo "Product quantity updated.";
        } else {
            echo "Failed to update product quantity.";
        }
    } else {
        // If the product doesn't exist, insert it with quantity 1
        $queryInsert = "INSERT INTO user_product (user_id, product_id, quantity) VALUES (:user_id, :product_id, 1)";
        $stmtInsert = $conn->prepare($queryInsert);
        $stmtInsert->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmtInsert->bindParam(':product_id', $product_id, PDO::PARAM_INT);

        if ($stmtInsert->execute()) {
            echo "Product added to cart.";
        } else {
            echo "Failed to add product to cart.";
        }
    }
} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
    exit();
} catch (Exception $e) {
    // Handle general errors
    echo "Error: " . $e->getMessage();
    exit();
}
?>




