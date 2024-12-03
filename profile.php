<?php
session_start();
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/User.php';


// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['email']) || $_SESSION['email'] === 'admin@admin.com') {
    header('Location: login.php');
    exit();
}

$email = $_SESSION['email'];

// Maak database- en gebruikersobjecten
$database = new Database();
$conn = $database->getConnection();
$user = new User($conn);

// Fout- en succesberichten
$error = '';
$success = '';

// Haal de huidige gebruiker op
$currentUser = $user->getUserByEmail($email);

if (!$currentUser) {
    $error = 'Gebruiker niet gevonden.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newEmail = $_POST['email'];
    $oldPassword = $_POST['old_password'];
    $newPassword = !empty($_POST['new_password']) ? password_hash($_POST['new_password'], PASSWORD_DEFAULT) : null;

    // Controleer het oude wachtwoord
    if (password_verify($oldPassword, $currentUser['password'])) {
        // Controleer of het nieuwe e-mailadres al bestaat
        if ($newEmail !== $email && $user->emailExists($newEmail)) {
            $error = 'Dit e-mailadres is al in gebruik.';
        } else {
            // Update de gebruiker
            if ($user->updateUser($email, $newEmail, $newPassword)) {
                $_SESSION['email'] = $newEmail; // Werk de sessie bij
                $email = $newEmail; // Update de lokale variabele
                $success = 'Je profiel is succesvol bijgewerkt.';
            } else {
                $error = 'Er ging iets mis bij het bijwerken.';
            }
        }
    } else {
        $error = 'Het oude wachtwoord is onjuist.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Profiel</title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <!-- Navbar -->
    <?php
    if ($email === 'admin@admin.com') {
        include_once 'admin-navbar.php';
    } else {
        include_once 'navbar.php';
    }
    ?>

    <!-- Profielkader -->
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
        </div>
    </div>
</body>
</html>









