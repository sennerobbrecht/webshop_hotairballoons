<?php
session_start();
// Klassen laden
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Products.php';

// Controleer of gebruiker is ingelogd en admin is
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['email'] !== 'admin@admin.com') {
    header('Location: login.php');
    exit();
}

// Maak database- en product-objecten
$db = new Database();
$productManager = new Product($db);

// Vooraf gedefinieerde categorieën
$categories = ['Complete Ballonnen', 'Manden', 'Enveloppes', 'Accessoires', 'Burners'];

// Verwijder product als er een 'delete' parameter is
if (isset($_GET['delete'])) {
    try {
        $productManager->deleteProduct($_GET['delete']);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

// Product toevoegen of bewerken
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = $_POST['title'];
        $category = $_POST['category'];
        $description = $_POST['description'];
        $price = floatval($_POST['price']);
        $image = $_FILES['image']['name'];

        // Dynamisch pad voor uploadmap
        $target_dir = __DIR__ . "/uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($image);
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            throw new Exception("Fout: Het uploaden van het bestand is mislukt.");
        }

        // Als een product-id is ingesteld, wordt het product bewerkt
        if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
            // Product bewerken
            $productManager->updateProduct($_POST['product_id'], $title, $category, $description, $price, "uploads/" . basename($image));
        } else {
            // Product toevoegen
            $productManager->addProduct($title, $category, $description, $price, "uploads/" . basename($image));
        }

        // Redirect om dubbele inzendingen te voorkomen
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

// Haal producten op
$products = [];
try {
    $products = $productManager->getAllProducts();
} catch (Exception $e) {
    echo $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Producten</title>
    <link rel="stylesheet" href="css/products.css">
    <style>
        /* CSS voor de popups */
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="product-container">
            <div class="add-button-container">
                <button class="add-button" onclick="showAddPopup()">+</button>
                <p>Voeg een nieuw product toe</p>
            </div>

            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                        <h3><?= htmlspecialchars($product['title']) ?></h3>
                        <p><?= htmlspecialchars($product['category']) ?></p>
                        <p><?= htmlspecialchars($product['description']) ?></p>
                        <p>€<?= number_format($product['price'], 2) ?></p>
                        <div class="product-actions">
                            <!-- Bewerken knop opent de bewerkingspopup -->
                            <a href="javascript:void(0);" onclick="showEditPopup(<?= $product['id'] ?>)">Bewerken</a>
                            <a href="?delete=<?= $product['id'] ?>">Verwijderen</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Er zijn geen producten om weer te geven.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Popup voor het toevoegen van een product -->
    <div class="popup" id="addProductPopup">
        <div class="popup-content">
            <h2>Nieuw Product Toevoegen</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value=""> <!-- Leeg bij toevoegen -->

                <label for="title">Titel</label>
                <input type="text" name="title" required>

                <label for="category">Categorie</label>
                <select name="category" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="image">Afbeelding</label>
                <input type="file" name="image" accept="image/*">

                <label for="description">Beschrijving</label>
                <textarea name="description" rows="4" required></textarea>

                <label for="price">Prijs</label>
                <input type="number" name="price" step="0.01" min="0" required>

                <button type="submit" name="add_product">Voeg Toe</button>
            </form>
        </div>
    </div>

    <!-- Popup voor het bewerken van een product -->
    <div class="popup" id="editProductPopup">
        <div class="popup-content">
            <h2>Product Bewerken</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="product_id" id="editProductId"> <!-- ID wordt hier ingevuld -->

                <label for="title">Titel</label>
                <input type="text" name="title" id="editTitle" required>

                <label for="category">Categorie</label>
                <select name="category" id="editCategory" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="image">Afbeelding</label>
                <input type="file" name="image" id="editImage" accept="image/*">

                <label for="description">Beschrijving</label>
                <textarea name="description" id="editDescription" rows="4" required></textarea>

                <label for="price">Prijs</label>
                <input type="number" name="price" id="editPrice" step="0.01" min="0" required>

                <button type="submit" name="edit_product">Wijzig Product</button> <!-- Wijzig product -->
            </form>
        </div>
    </div>

    <script>
        // Functie om de toevoegen-popup te tonen
        function showAddPopup() {
            document.getElementById('addProductPopup').style.display = 'flex';
        }

        // Functie om de bewerken-popup te tonen en de productgegevens in te vullen
        function showEditPopup(productId) {
            var products = <?php echo json_encode($products); ?>;
            var product = products.find(p => p.id === productId);

            if (product) {
                document.getElementById('editProductId').value = product.id;
                document.getElementById('editTitle').value = product.title;
                document.getElementById('editCategory').value = product.category;
                document.getElementById('editDescription').value = product.description;
                document.getElementById('editPrice').value = product.price;
            }

            document.getElementById('editProductPopup').style.display = 'flex';
        }
    </script>
</body>
</html>






















