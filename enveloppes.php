<?php
session_start();

// Controleer of de gebruiker is ingelogd
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit();
}

// Laad de benodigde klassen
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/category.php';

// Initialiseer de database en de Product-klasse
$db = new Database();
$productManager = new category($db);

// Haal de producten op voor de categorie 'Enveloppes'
$producten = $productManager->getProductsByCategory('Enveloppes');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enveloppes</title>
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
    <h1>Enveloppes</h1>
    <div class="divider"></div>

    <!-- Producten -->
    <div class="product-grid">
        <?php if (!empty($producten)): ?>
            <?php foreach ($producten as $product): ?>
                <div class="product-card">
                    <!-- Verpak de productkaart in een <a>-tag die naar product.php leidt met het product-ID -->
                    <a href="product.php?id=<?php echo $product['id']; ?>">
                        <img src="<?php echo htmlspecialchars($product['afbeelding']); ?>" alt="<?php echo htmlspecialchars($product['titel']); ?>">
                        <div class="content">
                            <h2><?php echo htmlspecialchars($product['titel']); ?></h2>
                            <p><?php echo htmlspecialchars($product['beschrijving']); ?></p>
                            <div class="price"><?php echo htmlspecialchars($product['prijs']); ?></div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; font-size: 18px; color: #666;">Er zijn nog geen producten beschikbaar.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
