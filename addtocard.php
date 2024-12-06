<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['email']) || $_SESSION['email'] === 'admin@admin.com') {
    header('Location: login.php');
    exit();
}

$email = $_SESSION['email'];





if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['title'], $_POST['price'], $_POST['image'])) {
    $productId = intval($_POST['id']);
    $title = htmlspecialchars($_POST['title']);
    $price = floatval($_POST['price']);
    $image = htmlspecialchars($_POST['image']);

   
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }


    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] += 1;
    } else {
      
        $_SESSION['cart'][$productId] = [
            'id' => $productId,
            'title' => $title,
            'price' => $price,
            'image' => $image,
            'quantity' => 1
        ];
    }

    echo json_encode(['status' => 'success', 'message' => 'Product toegevoegd aan winkelwagentje.']);
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Ongeldige invoer.']);
exit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>