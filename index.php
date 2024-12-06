<?php 
session_start();




if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

$email = $_SESSION['email'] ?? '';


require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Products.php';

$db = new Database();
$productManager = new Product($db);
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
    
    if ($email === 'admin@admin.com') {
        include_once 'admin-navbar.php';
    } else {
        include_once 'navbar.php';
    }
?>


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
         
            $latestProductsResult = $productManager->getLatestProducts();

            if ($latestProductsResult->num_rows > 0) {
                while ($product = $latestProductsResult->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '<a href="product.php?id=' . $product['id'] . '"><img src="' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['title']) . '" class="product-image"></a>';
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


<section class="all-products">
    <h2 class="section-title">Alle artikelen</h2>

    <form method="GET" action="">
        <label for="category">Filter op categorie:</label>
        <select name="category" id="category">
            <option value="">Alles</option>
            <option value="Complete Ballonnen" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Complete Ballonnen') ? 'selected' : ''; ?>>Complete Ballonnen</option>
            <option value="Manden" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Manden') ? 'selected' : ''; ?>>Manden</option>
            <option value="Enveloppes" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Enveloppes') ? 'selected' : ''; ?>>Enveloppes</option>
            <option value="Accessoires" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Accessoires') ? 'selected' : ''; ?>>Accessoires</option>
            <option value="Burners" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Burners') ? 'selected' : ''; ?>>Burners</option>
        </select>
        <button type="submit">Filteren</button>
    </form>

    <div class="products-grid">
        <?php
        
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        $allProductsResult = $productManager->getProductsByCategory($category);

      
        if ($allProductsResult->num_rows > 0) {
            while ($product = $allProductsResult->fetch_assoc()) {
                echo '<div class="product-card">';
                echo '<a href="product.php?id=' . $product['id'] . '"><img src="' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['title']) . '" class="product-image"></a>';
                echo '<h3 class="product-title">' . htmlspecialchars($product['title']) . '</h3>';
                echo '<p class="product-price">€' . htmlspecialchars($product['price']) . '</p>';
                echo '</div>';
            }
        } else {
            echo '<p>Geen producten beschikbaar.</p>';
        }
        ?>
    </div>
</section>

<script src="javascript/index.js"></script>

</body>
</html>




