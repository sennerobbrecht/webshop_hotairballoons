<?php
session_start();
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['product_id']) && isset($_POST['quantity']) && is_numeric($_POST['product_id']) && is_numeric($_POST['quantity'])) {
    $productId = intval($_POST['product_id']);
    $newQuantity = max(1, intval($_POST['quantity'])); 


    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] = $newQuantity;
    }

    $totalAmount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $totalAmount += $item['price'] * $item['quantity'];
    }

   
    echo number_format($totalAmount, 2, ',', '.');
}
?>
