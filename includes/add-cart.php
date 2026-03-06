<?php

include("db.php");

$product_id = $_GET['id'];

$user_id = 1;
$quantity = 1;

$query = "INSERT INTO cart (user_id, product_id, quantity)
VALUES ('$user_id','$product_id','$quantity')";

mysqli_query($conn,$query);

header("Location: ../index.php");

?>