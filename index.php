<?php 
  
  session_start(); 

  
  $isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
  $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';



?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="stylesheet" href="css/webshop.css">
</head>
<body >
<?php include_once 'navbar.php'; ?>
</body>
</html>

