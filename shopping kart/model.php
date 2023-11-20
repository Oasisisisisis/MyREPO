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

function addProduct($name, $price, $detail, $remain)
{
    global $db;
    $sql = "insert into product (name, price, detail, remain) values (?, ?, ?, ?)";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $name, $price, $detail, $remain);
    mysqli_stmt_execute($stmt);
    return true;
}

function updateProduct($id, $name, $price, $detail)
{
    global $db;
    $sql = "update product set name=?, price=?, detail=? where id=?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $name, $price, $detail, $id);
    mysqli_stmt_execute($stmt);
    return true;
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
?>