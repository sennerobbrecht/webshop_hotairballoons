<?php
if(!empty($_POST)){
    $email = $_POST['email'];
	$password = $_POST['password'];

	$options = [
		'cost' => 12,
	];
	 $hash = password_hash($password, PASSWORD_DEFAULT , $options);
	
	 $conn = new PDO('mysql:host=localhost;dbname=webshop_hotairballoons', 'root', '');
	$statement = $conn->prepare('INSERT INTO users(email, password) VALUES(:email, :password)');
	$statement->bindValue(':email', $email);
	$statement->bindValue(':password', $hash);
	$statement->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="css/webshop.css">
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
            <?php if (isset($error) && $error): ?>
                <p style="color: red;">Invalid email or password!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>