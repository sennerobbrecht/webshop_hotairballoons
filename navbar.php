<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body class="bodyindex">
    <nav class="navbar">
        <div class="menu-items">
            <a class="a" href="#">Luchtballonnen</a>
            <a class="a" href="#">Manden</a>
            <a class="a" href="#">Enveloppes</a>
            <a class="a" href="#">Accessoires</a>
        </div>
        <a class="a" href="logout.php">Hi <?php echo htmlspecialchars(explode('@', $_SESSION['email'])[0]); ?>, logout?</a>
    </nav>
</body>
</html>