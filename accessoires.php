<?php
session_start();

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

$email = $_SESSION['email'] ?? '';

// Klassen laden
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/category.php';


// Initialiseer de database en productklasse
$db = new Database();
$productManager = new Category($db);

// Haal producten op voor de categorie "Accessoires"
$producten = $productManager->getProductsByCategory('Accessoires');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accessoires</title>
    <link rel="stylesheet" href="css/category.css">
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

<div class="container">
    <h1>Accessoires</h1>
    <div class="divider"></div>

    <!-- Producten -->
    <div class="product-grid">
        <?php if (!empty($producten)): ?>
            <?php foreach ($producten as $product): ?>
                <div class="product-card">
                    <!-- Voeg een link toe rondom de productafbeelding en titel -->
                    <a href="product.php?id=<?php echo urlencode($product['id']); ?>">
                        <img src="<?php echo htmlspecialchars($product['afbeelding']); ?>" alt="<?php echo htmlspecialchars($product['titel']); ?>">
                        <div class="content">
                            <h2><?php echo htmlspecialchars($product['titel']); ?></h2>
                            <p><?php echo htmlspecialchars($product['beschrijving']); ?></p>
                            <div class="price"><?php echo htmlspecialchars($product['prijs']); ?></div>
                        </div>
                    </a>
                    <button>Voeg toe</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; font-size: 18px; color: #666;">Er zijn nog geen producten beschikbaar.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

