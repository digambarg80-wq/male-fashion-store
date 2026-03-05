<?php
include("../includes/db.php");

if(isset($_POST['submit'])){

$name=$_POST['name'];
$price=$_POST['price'];

$query="INSERT INTO products(name,price)
VALUES('$name','$price')";

mysqli_query($conn,$query);

echo "Product Added";

}
?>

<form method="POST">

<input type="text" name="name" placeholder="Product Name">

<input type="text" name="price" placeholder="Price">

<button name="submit">Add Product</button>

</form>