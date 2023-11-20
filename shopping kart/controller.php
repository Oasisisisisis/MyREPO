<?php
include 'dbconfig.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["action"] == "addProduct") {
        $name = $_POST["name"];
        $price = $_POST["price"];
        $detail = $_POST["detail"];
        $remain = $_POST["remain"]; 


        // 插入商品數據到資料庫
        $query = "INSERT INTO product (name, price, detail, remain) VALUES ('$name', '$price', '$detail', '$remain')";
        $result = mysqli_query($db, $query);

        if ($result) {
            echo "Product added successfully!";
        } else {
            echo "Error adding product: " . mysqli_error($db);
        }
    }
}
?>
