<?php
session_start();
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit();
}

// Databaseverbinding  
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webshop_hotairballoons";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Controleer of de 'category'-kolom bestaat en voeg toe indien nodig
$checkColumnQuery = "SHOW COLUMNS FROM products LIKE 'category'";
$result = $conn->query($checkColumnQuery);
if ($result->num_rows === 0) {
    $alterTableQuery = "ALTER TABLE products ADD category VARCHAR(255) NOT NULL";
    $conn->query($alterTableQuery);
}

// Predefined categories (met de nieuwe categorie "Burners")
$categories = ['Complete Ballonnen', 'Manden', 'Enveloppes', 'Accessoires', 'Burners'];

// Product toevoegen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $category = $conn->real_escape_string($_POST['category']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = floatval($_POST['price']);
    $image = $_FILES['image']['name'];

    // Validatie en upload van afbeelding
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Maak de map aan indien deze niet bestaat
    }
    $target_file = $target_dir . basename($image);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Controleer of het bestand een afbeelding is
    $check = getimagesize($_FILES['image']['tmp_name']);
    if ($check === false) {
        die("Fout: Het bestand is geen afbeelding.");
    }

    // Controleer bestandstype
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowed_types)) {
        die("Fout: Alleen JPG, JPEG, PNG en GIF zijn toegestaan.");
    }

    // Upload bestand
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        die("Fout: Het uploaden van het bestand is mislukt.");
    }

    // SQL-query uitvoeren
    $stmt = $conn->prepare("INSERT INTO products (title, category, image, description, price) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssd", $title, $category, $target_file, $description, $price);
    if ($stmt->execute()) {
        // Redirect na succesvol toevoegen
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        die("Fout bij het toevoegen van product: " . $stmt->error);
    }
    $stmt->close();
}

// Product verwijderen
if (isset($_GET['delete'])) {
    $product_id = intval($_GET['delete']);
    $deleteQuery = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $product_id);
    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        die("Fout bij het verwijderen van product: " . $stmt->error);
    }
}

// Product bijwerken (als de update-knop wordt ingedrukt)
if (isset($_POST['update_product'])) {
    $product_id = intval($_POST['product_id']);
    $title = $conn->real_escape_string($_POST['title']);
    $category = $conn->real_escape_string($_POST['category']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = floatval($_POST['price']);
    if ($price < 0) {
        die("Fout: De prijs kan niet negatief zijn.");
    }
    

    // Afbeelding bijwerken als een nieuwe afbeelding is geüpload
    $image = $_FILES['image']['name'] ? $_FILES['image']['name'] : $_POST['existing_image'];

    if ($image) {
        // Validatie en upload van afbeelding
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Maak de map aan indien deze niet bestaat
        }
        $target_file = $target_dir . basename($image);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Controleer of het bestand een afbeelding is
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check === false) {
            die("Fout: Het bestand is geen afbeelding.");
        }

        // Controleer bestandstype
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            die("Fout: Alleen JPG, JPEG, PNG en GIF zijn toegestaan.");
        }

        // Upload bestand
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            die("Fout: Het uploaden van het bestand is mislukt.");
        }
    }

    $updateQuery = "UPDATE products SET title = ?, category = ?, image = ?, description = ?, price = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssdi", $title, $category, $target_file, $description, $price, $product_id);
    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        die("Fout bij het bijwerken van product: " . $stmt->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Producten</title>
    <link rel="stylesheet" href="css/products.css">
</head>
<body>

    <div class="container">
   
        <div class="product-container">
            <div class="add-button-container">
                <button class="add-button" onclick="showPopup()">+</button>
                <p>Voeg een nieuw product toe</p>
            </div>

            <?php
            // Haal de producten op uit de database
            $query = "SELECT * FROM products";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product">';
                    echo '<img src="' . $row['image'] . '" alt="' . $row['title'] . '">';
                    echo '<h3>' . $row['title'] . '</h3>';
                    echo '<p>' . $row['category'] . '</p>';
                    echo '<p>' . $row['description'] . '</p>';
                    echo '<p>€' . number_format($row['price'], 2) . '</p>';
                    echo '<div class="product-actions">
                        <a href="edit_product.php?id=' . $row['id'] . '">Bewerken</a>
                        <a href="?delete=' . $row['id'] . '">Verwijderen</a>
                    </div>';
                    echo '</div>';
                }
            } else {
                echo '<p>Er zijn geen producten om weer te geven.</p>';
            }
            ?>
        </div>
    </div>

    <!-- Popup voor het toevoegen van een product -->
    <div class="popup" id="addProductPopup">
        <div class="popup-content">
            <h2>Nieuw Product Toevoegen</h2>
            <form method="POST" enctype="multipart/form-data">
                <label for="title">Titel</label>
                <input type="text" name="title" required>

                <label for="category">Categorie</label>
                <select name="category" required>
                    <?php
                    foreach ($categories as $category) {
                        echo "<option value='$category'>$category</option>";
                    }
                    ?>
                </select>

                <label for="image">Afbeelding</label>
                <input type="file" name="image" accept="image/*" required>

                <label for="description">Beschrijving</label>
                <textarea name="description" rows="4" required></textarea>

                <label for="price">Prijs</label>
                <input type="number" name="price" step="0.01"min="0" required>

                <button type="submit" name="add_product">Voeg Toe</button>
            </form>
        </div>
    </div>

    <script>
        function showPopup() {
            document.getElementById('addProductPopup').style.display = 'flex';
        }

        function closePopup() {
            document.getElementById('addProductPopup').style.display = 'none';
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>














