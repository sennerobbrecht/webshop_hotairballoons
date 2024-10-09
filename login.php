<?php
function canLogin($p_email, $p_password){
    return $p_email === "senne.robbrecht@outlook.be" && $p_password === "sennesenne";
}

// When we log in
if(!empty($_POST)){
    $email = $_POST['email'];
    $password = $_POST['password'];

    if(canLogin($email, $password)){
        $salt = "55648sferelmmsnn'§(è(yy$^ùùdfkhf";
        $cookieValue = $email . "," . md5($email . $salt);
        setcookie("login", $cookieValue, time() + 60 * 60 * 24 * 30);
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
    <link rel="stylesheet" href="css/webshop.css">
</head>
<body class="bodylogin">
    <div class="left"></div>
    <div class="right">
        <div class="login-form">
            <h2>Login</h2>
            <form method="POST" action="">
                <input type="email" name="email" placeholder="E-mail" required>
                <input type="password" name="password" placeholder="Wachtwoord" required>
                <button type="submit">Login</button>
            </form>
            <?php if (isset($error) && $error): ?>
                <p style="color: red;">Invalid email or password!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>


