<?php
session_start();
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

// Gebruiker niet ingelogd? Terugsturen naar loginpagina
if (!$isLoggedIn) {
    header('Location: login.php');
    exit();
}

// Databaseverbinding
$conn = new PDO('mysql:host=localhost;dbname=webshop_hotairballoons', 'root', '');

// Foutmelding en succesvariabelen
$error = '';
$success = '';

// Haal de huidige gegevens van de gebruiker op
$stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
$stmt->bindValue(':email', $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $error = 'Gebruiker niet gevonden.';
}

// Als het formulier wordt verzonden
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newEmail = $_POST['email'];
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];

    // Controleer of het oude wachtwoord klopt
    if (password_verify($oldPassword, $user['password'])) {
        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); // Nieuw wachtwoord hashen
        } else {
            $hashedPassword = $user['password']; // Wachtwoord ongewijzigd laten
        }

        // Controleer of het e-mailadres is gewijzigd
        if ($newEmail !== $email) {
            // Controleer of het nieuwe e-mailadres al bestaat
            $checkStmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $checkStmt->bindValue(':email', $newEmail);
            $checkStmt->execute();

            if ($checkStmt->rowCount() > 0) {
                $error = 'Dit e-mailadres is al in gebruik.';
            } else {
                // Bijwerken van het e-mailadres en wachtwoord
                $updateStmt = $conn->prepare("UPDATE users SET email = :new_email, password = :password WHERE email = :current_email");
                $updateStmt->bindValue(':new_email', $newEmail);
                $updateStmt->bindValue(':password', $hashedPassword);
                $updateStmt->bindValue(':current_email', $email);

                if ($updateStmt->execute()) {
                    $_SESSION['email'] = $newEmail; // Update de sessievariabele
                    $email = $newEmail; // Werk de lokale variabele bij
                    $success = 'Je profiel is succesvol bijgewerkt.';
                } else {
                    $error = 'Er ging iets mis bij het bijwerken.';
                }
            }
        } else {
            // Alleen wachtwoord bijwerken
            $updateStmt = $conn->prepare("UPDATE users SET password = :password WHERE email = :email");
            $updateStmt->bindValue(':password', $hashedPassword);
            $updateStmt->bindValue(':email', $email);

            if ($updateStmt->execute()) {
                $success = 'Je wachtwoord is succesvol bijgewerkt.';
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
    // Controleer of de ingelogde gebruiker de admin is
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







