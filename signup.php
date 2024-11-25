<?php
 $isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
 $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$error = false; 

if (!empty($_POST)) {
    $email = $_POST['email'];
    $password = $_POST['password'];

   
    $conn = new PDO('mysql:host=localhost;dbname=webshop_hotairballoons', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   
    $statement = $conn->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
    $statement->bindValue(':email', $email);
    $statement->execute();
    $emailExists = $statement->fetchColumn() > 0;

    if ($emailExists) {
        $error = 'This E-mail already exists. Please log in.';
    } else {
        
        $options = ['cost' => 12];
        $hash = password_hash($password, PASSWORD_DEFAULT, $options);

        
        $statement = $conn->prepare('INSERT INTO users(email, password) VALUES(:email, :password)');
        $statement->bindValue(':email', $email);
        $statement->bindValue(':password', $hash);
        $statement->execute();

       
        header('Location: login.php');
        exit;
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
