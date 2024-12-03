<?php
session_start();
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

// Inclusie van de benodigde klassen
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Products.php';  // Zorg ervoor dat de naam van de klasse klopt

// Maak verbinding met de database via de Database klasse
$database = new Database();
$product = new Product($database);

// Haal de zoekterm op uit de URL
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Zoek naar producten
$result = $product->searchProducts($query);

// Controleer of er producten zijn
$producten = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $producten[] = [
            'id' => $row['id'],
            'afbeelding' => $row['image'],
            'titel' => $row['title'],
            'beschrijving' => $row['description'],
            'prijs' => '€' . number_format($row['price'], 2, ',', '.')
        ];
    }
} else {
    $message = "Geen producten gevonden voor '$query'.";
}

// Sluit de databaseverbinding
$database->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoekresultaten</title>
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
    <h1>Zoekresultaten voor: <?php echo htmlspecialchars($query); ?></h1>
    <div class="divider"></div>

    <!-- Producten -->
    <div class="product-grid">
        <?php if (!empty($producten)): ?>
            <?php foreach ($producten as $product): ?>
                <div class="product-card">
                    <!-- Link naar de productpagina -->
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
            <p style="text-align: center; font-size: 18px; color: #666;"><?php echo $message ?? 'Geen producten gevonden.'; ?></p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>


