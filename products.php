<?php
session_start();
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Products.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['email'] !== 'admin@admin.com') {
    header('Location: login.php');
    exit();
}

$email = $_SESSION['email'] ?? '';

$db = new Database();
$productManager = new Product($db->getConnection());

$categories = ['Complete Ballonnen', 'Manden', 'Enveloppes', 'Accessoires', 'Burners'];

if (isset($_GET['delete'])) {
    try {
        $productManager->deleteProduct($_GET['delete']);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = $_POST['title'];
        $category = $_POST['category'];
        $description = $_POST['description'];
        $price = floatval($_POST['price']);
        $imagePath = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $target_dir = __DIR__ . "/uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $imagePath = "uploads/" . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . basename($_FILES['image']['name']));
        }

        if (isset($_POST['action']) && $_POST['action'] === 'edit') {
            $productId = intval($_POST['product_id']);

            if (empty($imagePath)) {
                $existingProduct = $productManager->getProductById($productId);
                $imagePath = $existingProduct['image'];
            }

            $productManager->updateProduct($productId, $title, $category, $description, $price, $imagePath);
        } else {
            $productManager->addProduct($title, $category, $description, $price, $imagePath);
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

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
</head>
<body>

    <div class="container">
    <?php
        if ($email === 'admin@admin.com') {
            include_once 'admin-navbar.php';
        } else {
            include_once 'navbar.php';
        }
    ?>
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
                            <button onclick="showEditPopup(<?= htmlspecialchars(json_encode($product)) ?>)">Bewerken</button>
                            <a href="?delete=<?= $product['id'] ?>">Verwijderen</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Er zijn geen producten om weer te geven.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="popup" id="addProductPopup">
        <div class="popup-content">
            <h2>Nieuw Product Toevoegen</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
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
                <button type="submit">Voeg Toe</button>
            </form>
        </div>
    </div>

    <div class="popup" id="editProductPopup">
        <div class="popup-content">
            <h2>Product Bewerken</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="product_id" id="editProductId">
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
                <button type="submit">Wijzig Product</button>
            </form>
        </div>
    </div>

    <script src="javascript/products.js"></script>
</body>
</html>
























