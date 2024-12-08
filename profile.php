<?php
session_start();
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/User.php';
require_once __DIR__ . '/classes/order.php';  

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['email']) || $_SESSION['email'] === 'admin@admin.com') {
    header('Location: login.php');
    exit();
}

$email = $_SESSION['email'];

$database = new Database();
$conn = $database->getConnection();
$user = new User($conn);
$order = new Order($conn);

$error = '';
$success = '';

$currentUser = $user->getUserByEmail($email);

if (!$currentUser) {
    $error = 'Gebruiker niet gevonden.';
}

$orders = $order->getOrdersByEmail($email);

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['old_password'], $_POST['new_password'])) {
        $oldPassword = $_POST['old_password'];
        $newPassword = $_POST['new_password'];
        
        // Check old password
        if (password_verify($oldPassword, $currentUser['password'])) {
            if ($user->updatePassword($email, $newPassword)) {
                $success = 'Wachtwoord succesvol bijgewerkt!';
            } else {
                $error = 'Fout bij het bijwerken van het wachtwoord.';
            }
        } else {
            $error = 'Oud wachtwoord is niet correct.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Profiel</title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <?php
    if ($email === 'admin@admin.com') {
        include_once 'admin-navbar.php';
    } else {
        include_once 'navbar.php';
    }
    ?>

    <div class="container">
        <div class="profile-container">
            <h2>Mijn Profiel</h2>
            <form method="POST" action="">
                <label for="email">E-mailadres:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                
                <label for="old_password">Oud Wachtwoord:</label>
                <input type="password" id="old_password" name="old_password" placeholder="Voer je oude wachtwoord in" required>
                
                <label for="new_password">Nieuw Wachtwoord:</label>
                <input type="password" id="new_password" name="new_password" placeholder="Voer een nieuw wachtwoord in">
                
                <button type="submit">Bijwerken</button>
            </form>

            <?php if (!empty($error)): ?>
                <p class="message error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <p class="message success"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>

            <h3>Mijn Bestellingen</h3>
            <?php if (!empty($orders)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Product Naam</th>
                            <th>Hoeveelheid</th>
                            <th>Bedrag</th>
                            <th>Besteldatum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $orderItem): ?>
                            <tr>
                                <td><?php echo isset($orderItem['product_name']) ? htmlspecialchars($orderItem['product_name']) : 'Onbekend'; ?></td>
                                <td><?php echo isset($orderItem['quantity']) ? htmlspecialchars($orderItem['quantity']) : '0'; ?></td>
                                <td>€<?php echo number_format($orderItem['price'] * $orderItem['quantity'], 2, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($orderItem['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Je hebt nog geen bestellingen geplaatst.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>












