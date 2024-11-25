<?php
  
    $isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
    $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
   
    ?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar met Sidebar</title>
    <link rel="stylesheet" href="css/web.css">
</head>
<body class="bodyindex">
    <nav class="navbar">
        <!-- Links: Welkom bericht -->
        <div class="navbar-left">
            <span class="welcome">Hi <?php echo htmlspecialchars(explode('@', $_SESSION['email'])[0]); ?>!</span>
        </div>

        <!-- Midden: Zoekbalk -->
        <div class="navbar-center">
            <form action="search.php" method="GET" class="search-form">
                <input type="text" name="query" placeholder="Zoek producten..." class="search-input">
                <button type="submit" class="search-button">Zoeken</button>
            </form>
        </div>

        <!-- Rechts: Hamburgermenu -->
        <div class="navbar-right">
            <div class="hamburger-menu" id="hamburgerMenu">
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="close-btn" id="closeBtn">&times;</div>
        <a href="index.php">Home</a>
        <a href="balloons.php">Luchtballonnen</a>
        <a href="basket.php">Manden</a>
        <a href="enveloppes.php">Enveloppes</a>
        <a href="accessoires.php">Accessoires</a>
        <a href="burners.php">Branders</a>
        <a href="products.php">Mijn Producten</a>
       
      
        <a href="logout.php">Uitloggen</a>
    </div>

    <script src="javascript/navbar.js"></script>
</body>
</html>