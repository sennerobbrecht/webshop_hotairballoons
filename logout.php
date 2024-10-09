<?php 
setcookie("login", "yes", time()-3600);
header('location: login.php');
?>