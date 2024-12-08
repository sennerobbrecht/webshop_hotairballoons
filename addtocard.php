<?php
session_start();


require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/User.php';
require_once __DIR__ . '/classes/Products.php';


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

$email = filter_var($_SESSION['email'], FILTER_SANITIZE_EMAIL);


$product_id = isset($_POST['product_id']) && is_numeric($_POST['product_id']) ? intval($_POST['product_id']) : 0;

if ($product_id <= 0) {
    echo "Invalid product ID.";
    exit();
}


try {
    $db = new Database();
    $conn = $db->getConnection();
    $user = new User($conn);

   
    $userId = $user->getUserIdByEmail($email);

    if (!$userId) {
        echo "User not found.";
        exit();
    }

    $queryCount = "SELECT COUNT(*) as count, quantity FROM user_product WHERE user_id = :user_id AND product_id = :product_id";
    $stmtCount = $conn->prepare($queryCount);
    $stmtCount->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtCount->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmtCount->execute();
    $result = $stmtCount->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
    
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

    echo "Error: " . $e->getMessage();
    exit();
} catch (Exception $e) {
 
    echo "Error: " . $e->getMessage();
    exit();
}
?>




