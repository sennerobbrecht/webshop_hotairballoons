<?php



$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit();
}


$mysqli = new mysqli("localhost", "root", "", "webshop_hotairballoons");


if ($mysqli->connect_error) {
    die("Fout bij verbinden met de database: " . $mysqli->connect_error);
}

$balance = 0;
if ($stmt = $mysqli->prepare("SELECT balance FROM users WHERE email = ?")) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($balance);
    $stmt->fetch();
    $stmt->close();
}


$_SESSION['balance'] = $balance;


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
        <div class="navbar-left">
            <span class="welcome">Hi <?php echo htmlspecialchars(explode('@', $_SESSION['email'])[0]); ?>!</span>
            <span class="admin-balance">Saldo: €<?php echo number_format($balance, 2, ',', '.'); ?></span>
        </div>

        <div class="navbar-center">
            <form action="search.php" method="GET" class="search-form">
                <input type="text" name="query" placeholder="Zoek producten..." class="search-input">
                <button type="submit" class="search-button">Zoeken</button>
            </form>
        </div>

        <div class="navbar-right">
            <div class="hamburger-menu" id="hamburgerMenu">
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
            </div>
        </div>
    </nav>

    <div class="sidebar" id="sidebar">
        <div class="close-btn" id="closeBtn">&times;</div>
        <a href="index.php">Home</a>
        <a href="balloons.php">Luchtballonnen</a>
        <a href="basket.php">Manden</a>
        <a href="enveloppes.php">Enveloppes</a>
        <a href="accessoires.php">Accessoires</a>
        <a href="burners.php">Branders</a>
        <a href="profile.php">Profiel</a>
        <a href="shopping_cart.php">Winkelmand</a>
        <a href="logout.php">Uitloggen</a>
    </div>

    <script src="javascript/navbar.js"></script>
</body>
</html>








