function addToCart(productId){

fetch("add-cart.php?id="+productId)
.then(res=>res.text())
.then(data=>{
alert("Product Added To Cart")
})

}