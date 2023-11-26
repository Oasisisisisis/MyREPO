<?php
require('dbconfig.php');

function getProductList()
{
    global $db;
    $sql = "select * from cart;";
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
		$sql = "update cart set name=?, price=?, detail=?, `remain`=? where id=?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "sissi", $name, $price, $detail, $remain, $id);
	} else {
		$sql = "insert into cart (name, price, detail, remain) values (?, ?, ?, ?)"; //SQL中的 ? 代表未來要用變數綁定進去的地方
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
    $sql = "delete from cart where id=?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    return true;
}
?>
