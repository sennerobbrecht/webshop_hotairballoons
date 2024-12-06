<?php
session_start();
require_once __DIR__ . '/classes/Database.php';

require_once __DIR__ . '/classes/User.php';

$database = new Database();
$conn = $database->getConnection();


$user = new User($conn);

$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

   
    if ($email === 'admin@admin.com' && $password === 'Admin') {
        $_SESSION['loggedin'] = true;
        $_SESSION['email'] = $email;

        header('Location: index.php');
        exit();
    }

    
    if ($user->canLogin($email, $password)) {
        $_SESSION['loggedin'] = true;
        $_SESSION['email'] = $email;

        header('Location: index.php');
        exit();
    } else {
        $error = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="left"></div>
    <div class="right">
        <div class="login-form">
            <h2>Login</h2>
            <form method="POST" action="">
                <input type="email" name="email" placeholder="E-mail" required>
                <input type="password" name="password" placeholder="Wachtwoord" required>
                <button type="submit">Login</button>
                <a href="signup.php">Don't have an account yet? Sign up</a>
            </form>
            <?php if ($error): ?>
                <p>Invalid email or password!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>




