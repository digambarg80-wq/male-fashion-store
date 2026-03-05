<?php
include("includes/db.php");

$query = "SELECT * FROM products";
$result = mysqli_query($conn,$query);
?>

<div class="products">

<?php while($row = mysqli_fetch_assoc($result)){ ?>

<div class="card">

<img src="images/<?php echo $row['image']; ?>">

<h3><?php echo $row['name']; ?></h3>

<p>₹<?php echo $row['price']; ?></p>

<a href="add-cart.php?id=<?php echo $row['id']; ?>">
    <button>Add to Cart</button>
</a>

</div>

<?php } ?>

</div>