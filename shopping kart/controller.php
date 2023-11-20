<?php
include 'dbconfig.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["action"] == "addProduct") {
        $name = $_POST["name"];
        $price = $_POST["price"];
        $detail = $_POST["detail"];

        // 將商品資訊插入資料庫
        $query = "INSERT INTO products (name, price, detail) VALUES ('$name', '$price', '$detail')";
        mysqli_query($db, $query) or die(mysqli_error($db));
        
        echo "Product added successfully!";
    }
}
?>
