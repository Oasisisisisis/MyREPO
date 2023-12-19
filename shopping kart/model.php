<?php
require('dbconfig.php');

function getProductList()
{
    global $db;
    $sql = "select * from product;";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $rows = array();
    while ($r = mysqli_fetch_assoc($result)) {
        $rows[] = $r;
    }
    return $rows;
}

function addProduct($name, $price, $detail, $remain, $id)
{
    global $db;
	if($id>0) {
		$sql = "update product set name=?, price=?, detail=?, `remain`=? where id=?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "sissi", $name, $price, $detail, $remain, $id);
	} else {
		$sql = "insert into product (name, price, detail, remain) values (?, ?, ?, ?)"; //SQL中的 ? 代表未來要用變數綁定進去的地方
		$stmt = mysqli_prepare($db, $sql); //prepare sql statement
		mysqli_stmt_bind_param($stmt, "sssi", $name, $price, $detail, $remain); //bind parameters with variables, with types "sss":string, string ,string
	}
    mysqli_stmt_execute($stmt);
    return true;
}

function updateProduct($id, $name, $price, $detail)
{
	echo $id, $name, $price, $detail;
	return;
}

function delProduct($id)
{
    global $db;
    $sql = "delete from product where id=?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    return true;
}

function addToCart($product_id, $quantity)
{
    global $db;
    
    $sql = "select * from cart where product_id=?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) == 0 ) {
        $sql = "insert into cart (product_id, quantity) value (?,?)";
		$stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $product_id, $quantity);
        mysqli_stmt_execute($stmt);
    }else{
        $sql = "update cart set quantity=? where product_id=?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $quantity, $product_id);
        mysqli_stmt_execute($stmt);

    }
    return true;
}

function removeFromCart($id)
{
    global $db;
    $sql = "delete from cart where id=?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    return true;
}

function getCartList()
{
    global $db;
    $sql = "select cart.id, product.name, product.price, cart.quantity from cart LEFT JOIN product ON product.id=cart.product_id";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $rows = array();
    while ($r = mysqli_fetch_assoc($result)) {
        $rows[] = $r;
    }
    return $rows;
}
?>
