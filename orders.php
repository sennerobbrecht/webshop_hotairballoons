<?php
session_start();


$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/order.php';



$db = new Database();
$conn = $db->getConnection();
$orderManager = new Order($conn);


$orders = $orderManager->getAllOrdersWithItems();


$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
<?php

if ($email === 'admin@admin.com') {
    include_once 'admin-navbar.php';
} else {
    include_once 'navbar.php';
}
?>
<h1>Orders</h1>

<?php

Order::renderOrdersTable($orders);
?>

</body>
</html>

