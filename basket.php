<?php
session_start();
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit();
}

// Dummy producten (in een echte situatie haal je dit uit een database)

// Verbinding met de database
$servername = "localhost";
$username = "root";
$password = ""; // Of je database-wachtwoord
$database = "webshop_hotairballoons";

$conn = new mysqli($servername, $username, $password, $database);

// Controleer verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Query om alle producten op te halen
$sql = "SELECT image, title, description, price FROM products WHERE category = 'Manden'";
$result = $conn->query($sql);

// Controleer of er resultaten zijn
$producten = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $producten[] = [
            'afbeelding' => $row['image'],
            'titel' => $row['title'],
            'beschrijving' => $row['description'],
            'prijs' => '€' . number_format($row['price'], 2, ',', '.')
        ];
    }
} else {
    echo "Geen producten gevonden.";
}

// Verbinding sluiten
$conn->close();

// $producten-array bevat nu de gegevens uit de database


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manden</title>
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
    <h1>Manden</h1>
    <div class="divider"></div>

    <!-- Producten -->
    <div class="product-grid">
        <?php if (!empty($producten)): ?>
            <?php foreach ($producten as $product): ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($product['afbeelding']); ?>" alt="<?php echo htmlspecialchars($product['titel']); ?>">
                    <div class="content">
                        <h2><?php echo htmlspecialchars($product['titel']); ?></h2>
                        <p><?php echo htmlspecialchars($product['beschrijving']); ?></p>
                        <div class="price"><?php echo htmlspecialchars($product['prijs']); ?></div>
                        <button>Voeg toe</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; font-size: 18px; color: #666;">Er zijn nog geen producten beschikbaar.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>