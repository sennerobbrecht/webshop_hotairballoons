<?php
	function canLogin($p_email, $p_password){
		$conn = new PDO('mysql:host=localhost;dbname=webshop_hotairballoons', 'root', '');
		$statement= $conn->prepare('SELECT * FROM users WHERE email = :email');
		$statement->bindValue(':email', $p_email);
		$statement->execute();
		$user = $statement->fetch(PDO::FETCH_ASSOC);
		
		if($user){
			$hash= $user['password'];
			if(password_verify($p_password, $hash)){
				return true;
			}else{
				return false;
			}
		}
		else{
			return false;
		}

}
 
if (!empty($_POST)) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Controleer of het admin is
    if ($email === 'admin@admin.com' && $password === 'Admin') {
        session_start();
        $_SESSION['loggedin'] = true;
        $_SESSION['email'] = $email;

        header('location: index.php');
        exit();
    }

    // Normale logincontrole
    if (canLogin($email, $password)) {
        session_start();
        $_SESSION['loggedin'] = true;
        $_SESSION['email'] = $email;

        header('location: index.php');
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
            <?php if (isset($error) && $error): ?>
                <p>Invalid email or password!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>



