<?php 
  //check if cookie login exist and contains "yes" 
  //else redirect to login.php
  if(!isset($_COOKIE['login'])){
    header('location: login.php');
  }
  else{
    $cookie = $_COOKIE['login'];
    $salt = "55648sferelmmsnn'§(è(yy$^ùùdfkhf";

    $pieces = explode(",", $cookie);
    if( md5($pieces[0].$salt) !== $pieces[1] ){
      header('location: login.php');
    }
  }



?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="stylesheet" href="css/webshop.css">
</head>
<body class="bodyindex">
    <nav class="navbar">
        <div class="menu-items">
            <a class="a" href="#">Luchtballonnen</a>
            <a class="a" href="#">Manden</a>
            <a class="a" href="#">Enveloppes</a>
            <a class="a" href="#">Accessoires</a>
        </div>
        <a class="a" href="logout.php">Logout</a>
    </nav>
</body>
</html>

