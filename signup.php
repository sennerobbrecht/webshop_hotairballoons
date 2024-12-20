<?php
session_start();
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/User.php';


$database = new Database();
$conn = $database->getConnection(); 


$user = new User($conn);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];


    $query = "SELECT COUNT(*) FROM users WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $emailExists = $stmt->fetchColumn();

    if ($emailExists > 0) {
        $error = 'This E-mail already exists. Please log in.';
    } else {
       
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);

        $query = "INSERT INTO users (email, password) VALUES (:email, :password)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);

        if ($stmt->execute()) {
            header('Location: login.php');
            exit();
        } else {
            $error = 'Something went wrong. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body class="bodylogin">
    <div class="left"></div>
    <div class="right">
        <div class="login-form">
            <h2>Sign up</h2>
            <form method="POST" action="">
                <input type="email" name="email" placeholder="E-mail" required>
                <input type="password" name="password" placeholder="Wachtwoord" required>
                <button type="submit">Sign up</button>
                <a href="login.php">Already have an account? Sign in</a>
            </form>
            
            <?php if ($error): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>



