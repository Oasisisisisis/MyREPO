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
function addToCart($product_id) 
{
    global $db;

    // 查詢是否有一樣的商品
    $selectSql = "SELECT * FROM cart WHERE product_id=?";
    $selectStmt = mysqli_prepare($db, $selectSql);
    mysqli_stmt_bind_param($selectStmt, "i", $product_id);
    mysqli_stmt_execute($selectStmt);
    $selectResult = mysqli_stmt_get_result($selectStmt);

    if (mysqli_num_rows($selectResult) == 0) {
        // 新加入一筆資料
        $insertSql = "INSERT INTO cart (product_id, quantity) VALUES (?, 1)";
        $insertStmt = mysqli_prepare($db, $insertSql);
        mysqli_stmt_bind_param($insertStmt, "i", $product_id);
        mysqli_stmt_execute($insertStmt);
        echo json_encode(["message" => "成功加入購物車"]);
    } else {
        // 獲取 product 資料表中的庫存數量
        $productSql = "SELECT remain FROM product WHERE id=?";
        $productStmt = mysqli_prepare($db, $productSql);
        mysqli_stmt_bind_param($productStmt, "i", $product_id);
        mysqli_stmt_execute($productStmt);
        $productResult = mysqli_stmt_get_result($productStmt);
        
        if ($productRow = mysqli_fetch_assoc($productResult)) {
            $productRemain = $productRow['remain'];
        } else {
            echo "Error fetching product information.";
            return;
        }

        // 查詢 cart 中的數量
        $cartSql = "SELECT quantity FROM cart WHERE product_id=?";
        $cartStmt = mysqli_prepare($db, $cartSql);
        mysqli_stmt_bind_param($cartStmt, "i", $product_id);
        mysqli_stmt_execute($cartStmt);
        $cartResult = mysqli_stmt_get_result($cartStmt);

        if ($cartRow = mysqli_fetch_assoc($cartResult)) {
            $cartQuantity = $cartRow['quantity'];

            // 檢查是否超過庫存上限
            if (($cartQuantity + 1) > $productRemain) {
                echo json_encode(["error" => "已達購買上限"]);
				return;
            }
        }

        // 更新 cart 資料表
        $updateSql = "UPDATE cart SET quantity=quantity+1 WHERE product_id=?";
        $updateStmt = mysqli_prepare($db, $updateSql);
        mysqli_stmt_bind_param($updateStmt, "i", $product_id);
        mysqli_stmt_execute($updateStmt);
		echo json_encode(["message" => "成功加入購物車"]);
    }
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
