<?php
require('model.php'); 

$act = $_REQUEST['act'];
switch ($act) {
    case "listProduct":
        $products = getProductList();
		if ($products === false) {
            echo "Error fetching product list.";
        } else {
            echo json_encode($products);
        }
        return;
    case "addProduct":
        $jsonStr = $_POST['dat'];
        $product = json_decode($jsonStr);
        // 需要進行驗證
        addProduct($product->name, $product->price, $product->detail, $product->remain, $product->id);
        return;
    case "del":
        $id = (int)$_REQUEST['id'];
        // 驗證
        delProduct($id);
        return;
    default:
}

?>