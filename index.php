<?php 
  session_start(); 

  // Controleer of de gebruiker is ingelogd
  if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
  }

  $email = $_SESSION['email'] ?? '';

  // Databaseverbinding
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "webshop_hotairballoons";

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welkom in onze webshop</title>
    <link rel="stylesheet" href="css/web.css">
   
</head>
<body>
<?php
    // Controleer of de ingelogde gebruiker de admin is
    if ($email === 'admin@admin.com') {
        include_once 'admin-navbar.php';
    } else {
        include_once 'navbar.php';
    }
    ?>

    <!-- Hoofdtitel en afbeelding -->
    <section class="hero">
        <h1 class="hero-title">Welkom in onze webshop!</h1>
        <div class="hero-image">
            <img src="images/todi.jpg" alt="Webshop afbeelding" class="hero-photo">
        </div>
    </section>

    <hr class="section-divider">

    <section class="latest-products">
    <h2 class="section-title">Laatst toegevoegd</h2>
    <div class="carousel-container">
        <button class="carousel-button left" onclick="scrollCarousel(-1)">&#10094;</button>
        <div class="product-carousel">
            <?php
            $latestProductsQuery = "SELECT * FROM products ORDER BY id DESC LIMIT 5";
            $latestProductsResult = $conn->query($latestProductsQuery);

            if ($latestProductsResult->num_rows > 0) {
                while ($product = $latestProductsResult->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '<img src="' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['title']) . '" class="product-image">';
                    echo '<h3 class="product-title">' . htmlspecialchars($product['title']) . '</h3>';
                    echo '</div>';
                }
            } else {
                echo '<p>Geen producten beschikbaar.</p>';
            }
            ?>
        </div>
        <button class="carousel-button right" onclick="scrollCarousel(1)">&#10095;</button>
    </div>
</section>


    <hr class="section-divider">

    <!-- Categorieën -->
    <section class="categories">
    <h2 class="section-title">Categorieën</h2>
    <div class="category-grid">
        <!-- Categorie 1 -->
        <div class="category-card">
            <a href="balloons.php">
                <img src="images/luchtballon.jpg" alt="Luchtballonnen" class="category-image">
                <h3 class="category-title">Luchtballonnen</h3>
            </a>
        </div>
        <!-- Categorie 2 -->
        <div class="category-card">
            <a href="basket.php">
                <img src="images/mand.jpg" alt="Manden" class="category-image">
                <h3 class="category-title">Manden</h3>
            </a>
        </div>
        <!-- Categorie 3 -->
        <div class="category-card">
            <a href="enveloppes.php">
                <img src="images/enveloppen.jpg" alt="Enveloppes" class="category-image">
                <h3 class="category-title">Enveloppes</h3>
            </a>
        </div>
        <!-- Categorie 4 -->
        <div class="category-card">
            <a href="accessoires.php">
                <img src="images/brander.jpg" alt="Accessoires" class="category-image">
                <h3 class="category-title">Accessoires</h3>
            </a>
        </div>
        <div class="category-card">
            <a href="burners.php">
                <img src="images/brander.jpg" alt="Accessoires" class="category-image">
                <h3 class="category-title">Accessoires</h3>
            </a>
        </div>
    </div>
</section>
<script src="javascript/index.js" ></script>

</body>
</html>


